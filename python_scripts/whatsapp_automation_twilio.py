#!/usr/bin/env python3
"""
WhatsApp Automation Script for Subscription Reminders (Pakistan) - Using Twilio SDK
====================================================================================
This script sends WhatsApp reminders to members whose subscriptions are ending.
Configured for Pakistan phone numbers (format: 03XX-XXXXXXX, international: +92 3XX XXXXXXX).

Uses Twilio Python SDK for reliable message sending.

It runs periodically (every 30 minutes by default) and sends 3 reminders:
1. On the subscription end date
2. X days after end date (configurable, default 5)
3. Y days after 2nd reminder (configurable, default 7)

The script connects to MySQL database to:
- Check if automation is enabled
- Get members with active subscriptions ending
- Track message history to avoid duplicates
- Send WhatsApp messages via Twilio SDK
- Log all sent messages

Phone Number Format:
- Pakistan mobile numbers: 03XX-XXXXXXX (11 digits starting with 03)
- International format: +92 3XX XXXXXXX (removes leading 0, adds +92)
"""

import sys
import os
import json
import re
import logging
from datetime import datetime, timedelta, date
from typing import Optional, List, Dict, Tuple
import mysql.connector
from mysql.connector import Error
from twilio.rest import Client
from twilio.base.exceptions import TwilioRestException

# Add the parent directory to the path for imports if needed
sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))

# Database configuration
DB_CONFIG = {
    'host': '127.0.0.1',
    'port': 3306,
    'database': 'gymstation_db',
    'user': 'gymstation_user',
    'password': 'password'
}

# Ensure logs directory exists before configuring logging
LOGS_DIR = '/home/mint/gymstation/python_scripts/logs'
LOG_FILE = os.path.join(LOGS_DIR, 'whatsapp_automation_twilio.log')

# Create logs directory if it doesn't exist
os.makedirs(LOGS_DIR, exist_ok=True)

# Configure logging
logging.basicConfig(
    level=logging.INFO,
    format='%(asctime)s - %(name)s - %(levelname)s - %(message)s',
    handlers=[
        logging.FileHandler(LOG_FILE),
        logging.StreamHandler(sys.stdout)
    ]
)
logger = logging.getLogger(__name__)


class WhatsAppAutomation:
    """Main class for WhatsApp automation using Twilio SDK"""
    
    def __init__(self):
        self.db_connection = None
        self.settings = {}
        self.twilio_client = None
        
    def connect_to_database(self) -> bool:
        """Establish connection to MySQL database"""
        try:
            logger.info(f"Connecting to MySQL database at {DB_CONFIG['host']}:{DB_CONFIG['port']}...")
            self.db_connection = mysql.connector.connect(
                host=DB_CONFIG['host'],
                port=DB_CONFIG['port'],
                database=DB_CONFIG['database'],
                user=DB_CONFIG['user'],
                password=DB_CONFIG['password']
            )
            
            if self.db_connection.is_connected():
                logger.info("✓ Successfully connected to MySQL database")
                return True
        except Error as e:
            logger.error(f"✗ Error connecting to MySQL database: {e}")
            return False
        
        return False
    
    def get_setting(self, key: str, default: str = '') -> str:
        """Get a setting value from database"""
        if not self.db_connection or not self.db_connection.is_connected():
            return default
            
        try:
            cursor = self.db_connection.cursor(dictionary=True)
            cursor.execute("SELECT value FROM trn_settings WHERE `key` = %s", (key,))
            result = cursor.fetchone()
            cursor.close()
            
            if result:
                return result['value'] or default
            return default
        except Error as e:
            logger.error(f"Error getting setting {key}: {e}")
            return default
    
    def load_settings(self):
        """Load all WhatsApp settings from database"""
        self.settings = {
            'enabled': self.get_setting('whatsapp_automation_enabled', '0') == '1',
            'api_key': self.get_setting('whatsapp_api_key', ''),
            'api_secret': self.get_setting('whatsapp_api_secret', ''),
            'from_number': self.get_setting('whatsapp_from_number', ''),
            'reminder_2_days': int(self.get_setting('whatsapp_reminder_2_days', '5')),
            'reminder_3_days': int(self.get_setting('whatsapp_reminder_3_days', '7')),
            'reminder_1_message': self.get_setting('whatsapp_reminder_1_message', ''),
            'reminder_2_message': self.get_setting('whatsapp_reminder_2_message', ''),
            'reminder_3_message': self.get_setting('whatsapp_reminder_3_message', ''),
            'start_time': self.get_setting('whatsapp_start_time', '09:00'),
            'end_time': self.get_setting('whatsapp_end_time', '21:00'),
        }
        
        logger.info(f"Settings loaded - Enabled: {self.settings['enabled']}, Time Window: {self.settings['start_time']} - {self.settings['end_time']}")
    
    def initialize_twilio_client(self) -> bool:
        """Initialize Twilio client with credentials from settings"""
        try:
            if not self.settings['api_key'] or not self.settings['api_secret']:
                logger.error("Twilio Account SID or Auth Token not configured")
                return False
            
            self.twilio_client = Client(self.settings['api_key'], self.settings['api_secret'])
            logger.info("✓ Twilio client initialized successfully")
            return True
        except Exception as e:
            logger.error(f"✗ Error initializing Twilio client: {e}")
            return False
    
    def format_message(self, template: str, member_name: str, end_date: date, days_ago: int = 0) -> str:
        """Format message template with variables"""
        message = template
        message = message.replace('{member_name}', member_name)
        message = message.replace('{end_date}', end_date.strftime('%d-%m-%Y'))
        message = message.replace('{days_ago}', str(days_ago))
        return message
    
    def send_whatsapp_message(self, phone_number: str, message: str) -> Tuple[bool, str]:
        """
        Send WhatsApp message using Twilio SDK
        Uses the official Twilio Python SDK for reliable message delivery
        """
        if not self.twilio_client:
            return False, "Twilio client not initialized"
        
        if not self.settings['from_number']:
            return False, "WhatsApp from number not configured"
        
        try:
            # Clean phone number (remove spaces, dashes, etc.)
            phone_number = re.sub(r'[^\d+]', '', phone_number)
            
            # Ensure phone number starts with country code
            if not phone_number.startswith('+'):
                # Pakistan phone number format: 03XX-XXXXXXX (11 digits starting with 03)
                # Convert to international format: +92 3XX XXXXXXX (remove leading 0, add +92)
                if phone_number.startswith('0'):
                    # Remove leading 0 and add Pakistan country code +92
                    phone_number = '+92' + phone_number[1:]
                elif len(phone_number) == 10 and phone_number.startswith('3'):
                    # Already in format 3XX-XXXXXXX, just add country code
                    phone_number = '+92' + phone_number
                else:
                    # Assume it's a Pakistan number without leading 0
                    phone_number = '+92' + phone_number.lstrip('0')
            
            # Format phone numbers for WhatsApp
            from_whatsapp = f"whatsapp:{self.settings['from_number']}"
            to_whatsapp = f"whatsapp:{phone_number}"
            
            logger.info(f"Attempting to send message to {to_whatsapp} from {from_whatsapp}")
            
            # Send message using Twilio SDK
            twilio_message = self.twilio_client.messages.create(
                from_=from_whatsapp,
                body=message,
                to=to_whatsapp
            )
            
            logger.info(f"Message sent successfully to {phone_number}. SID: {twilio_message.sid}")
            logger.info(f"Message status: {twilio_message.status}")
            
            return True, f"Message sent successfully. SID: {twilio_message.sid}"
                
        except TwilioRestException as e:
            error_msg = f"Twilio API error: {e.msg} (Code: {e.code})"
            logger.error(f"Failed to send message to {phone_number}: {error_msg}")
            return False, error_msg
        except Exception as e:
            error_msg = f"Unexpected error: {str(e)}"
            logger.error(f"Failed to send message to {phone_number}: {error_msg}")
            return False, error_msg
    
    def get_members_needing_reminders(self) -> List[Dict]:
        """
        Get members who need reminders based on subscription end dates
        Returns list of members with their subscription information
        
        This includes:
        - Members with subscriptions ending soon (within reminder window)
        - Members with past-due subscriptions that have failed reminders (for retry)
        """
        if not self.db_connection or not self.db_connection.is_connected():
            return []
        
        try:
            cursor = self.db_connection.cursor(dictionary=True)
            today = date.today()
            
            # Calculate max days to look ahead for reminders
            max_days_ahead = self.settings['reminder_2_days'] + self.settings['reminder_3_days']
            
            # Calculate how far back to look for failed reminders (e.g., 30 days)
            max_days_back = 30  # Look back 30 days for failed reminders that need retry
            
            # Get active members with ongoing subscriptions
            # Status: 1 = Active, Subscription status: 1 = Ongoing
            # This query includes:
            # 1. Members with subscriptions ending soon (within reminder window)
            # 2. Members with past subscriptions that may have failed reminders
            query = """
                SELECT 
                    member_info.member_id,
                    member_info.member_name,
                    member_info.phone_number,
                    member_info.member_status,
                    (
                        SELECT s2.id 
                        FROM trn_subscriptions s2 
                        WHERE s2.member_id = member_info.member_id 
                            AND s2.status = 1 
                            AND s2.end_date = member_info.earliest_end_date
                        ORDER BY s2.id ASC
                        LIMIT 1
                    ) as subscription_id,
                    member_info.earliest_end_date
                FROM (
                    SELECT 
                        m.id as member_id,
                        m.name as member_name,
                        m.contact as phone_number,
                        m.status as member_status,
                        MIN(s.end_date) as earliest_end_date
                    FROM mst_members m
                    INNER JOIN trn_subscriptions s ON m.id = s.member_id
                    WHERE m.status = 1
                        AND s.status = 1
                        AND s.end_date BETWEEN DATE_SUB(%s, INTERVAL %s DAY) AND DATE_ADD(%s, INTERVAL %s DAY)
                    GROUP BY m.id, m.name, m.contact, m.status
                    HAVING MIN(s.end_date) IS NOT NULL
                ) as member_info
                ORDER BY member_info.earliest_end_date ASC
            """
            
            cursor.execute(query, (today, max_days_back, today, max_days_ahead))
            members = cursor.fetchall()
            cursor.close()
            
            logger.info(f"Found {len(members)} members with subscriptions ending (including past-due for retry)")
            return members
            
        except Error as e:
            logger.error(f"Error getting members needing reminders: {e}")
            return []
    
    def get_message_history(self, member_id: int, subscription_end_date: date) -> Dict:
        """
        Get message history for a member based on subscription end date
        Returns dict with reminder numbers that have been sent
        """
        if not self.db_connection or not self.db_connection.is_connected():
            return {}
        
        try:
            cursor = self.db_connection.cursor(dictionary=True)
            query = """
                SELECT reminder_number, sent_at, status
                FROM trn_whatsapp_message_history
                WHERE member_id = %s
                    AND subscription_end_date = %s
                ORDER BY reminder_number ASC
            """
            cursor.execute(query, (member_id, subscription_end_date))
            history = cursor.fetchall()
            cursor.close()
            
            # Convert to dict: {reminder_number: sent_at}
            result = {}
            for record in history:
                result[record['reminder_number']] = {
                    'sent_at': record['sent_at'],
                    'status': record['status']
                }
            
            return result
            
        except Error as e:
            logger.error(f"Error getting message history: {e}")
            return {}
    
    def should_send_reminder(self, member: Dict, reminder_number: int, history: Dict) -> Tuple[bool, Optional[date]]:
        """
        Determine if a reminder should be sent
        Returns (should_send, subscription_end_date)
        
        This method now checks message status to allow retry of failed messages:
        - Only considers reminders with status='sent' as successfully sent
        - Allows retry for failed reminders or reminders that were never sent
        - Handles past-due reminders that failed
        """
        today = date.today()
        subscription_end_date = member['earliest_end_date']
        
        # Helper function to check if a reminder was successfully sent
        def is_successfully_sent(rem_num: int) -> bool:
            if rem_num not in history:
                return False
            return history[rem_num].get('status') == 'sent'
        
        # Helper function to check if a reminder failed
        def has_failed(rem_num: int) -> bool:
            if rem_num not in history:
                return False
            return history[rem_num].get('status') == 'failed'
        
        if reminder_number == 1:
            # First reminder: send on end date
            if subscription_end_date == today:
                # Check if already successfully sent
                if not is_successfully_sent(1):
                    if has_failed(1):
                        logger.info(f"    ✓ Reminder 1 should be retried (end date is today, previous attempt failed)")
                    else:
                        logger.info(f"    ✓ Reminder 1 should be sent (end date is today and not sent yet)")
                    return True, subscription_end_date
                else:
                    logger.info(f"    ✗ Reminder 1 already sent successfully")
            elif subscription_end_date < today:
                # Past due: check if it failed and needs retry
                if has_failed(1):
                    logger.info(f"    ✓ Reminder 1 should be retried (end date passed, previous attempt failed)")
                    return True, subscription_end_date
                elif not is_successfully_sent(1):
                    logger.info(f"    ✓ Reminder 1 should be sent (end date passed, never sent)")
                    return True, subscription_end_date
            else:
                days_diff = (subscription_end_date - today).days
                logger.debug(f"    Reminder 1: end date is {days_diff} days in the future")
            return False, None
            
        elif reminder_number == 2:
            # Second reminder: send X days after end date
            target_date = subscription_end_date + timedelta(days=self.settings['reminder_2_days'])
            if target_date == today:
                # Check if first reminder was successfully sent and second not successfully sent
                if is_successfully_sent(1) and not is_successfully_sent(2):
                    if has_failed(2):
                        logger.info(f"    ✓ Reminder 2 should be retried (target date is today, reminder 1 sent, reminder 2 failed)")
                    else:
                        logger.info(f"    ✓ Reminder 2 should be sent (target date is today, reminder 1 sent, reminder 2 not sent)")
                    return True, subscription_end_date
                elif not is_successfully_sent(1):
                    logger.info(f"    ✗ Reminder 2: first reminder not sent successfully yet")
                else:
                    logger.info(f"    ✗ Reminder 2 already sent successfully")
            elif target_date < today:
                # Past due: check if it failed and needs retry
                if is_successfully_sent(1) and has_failed(2):
                    logger.info(f"    ✓ Reminder 2 should be retried (target date passed, reminder 1 sent, reminder 2 failed)")
                    return True, subscription_end_date
                elif is_successfully_sent(1) and not is_successfully_sent(2):
                    logger.info(f"    ✓ Reminder 2 should be sent (target date passed, reminder 1 sent, reminder 2 never sent)")
                    return True, subscription_end_date
            else:
                days_diff = (target_date - today).days
                logger.debug(f"    Reminder 2: target date is {days_diff} days in the future")
            return False, None
            
        elif reminder_number == 3:
            # Third reminder: send Y days after second reminder
            # Calculate: end_date + reminder_2_days + reminder_3_days
            second_reminder_date = subscription_end_date + timedelta(days=self.settings['reminder_2_days'])
            target_date = second_reminder_date + timedelta(days=self.settings['reminder_3_days'])
            if target_date == today:
                # Check if second reminder was successfully sent and third not successfully sent
                if is_successfully_sent(2) and not is_successfully_sent(3):
                    if has_failed(3):
                        logger.info(f"    ✓ Reminder 3 should be retried (target date is today, reminder 2 sent, reminder 3 failed)")
                    else:
                        logger.info(f"    ✓ Reminder 3 should be sent (target date is today, reminder 2 sent, reminder 3 not sent)")
                    return True, subscription_end_date
                elif not is_successfully_sent(2):
                    logger.info(f"    ✗ Reminder 3: second reminder not sent successfully yet")
                else:
                    logger.info(f"    ✗ Reminder 3 already sent successfully")
            elif target_date < today:
                # Past due: check if it failed and needs retry
                if is_successfully_sent(2) and has_failed(3):
                    logger.info(f"    ✓ Reminder 3 should be retried (target date passed, reminder 2 sent, reminder 3 failed)")
                    return True, subscription_end_date
                elif is_successfully_sent(2) and not is_successfully_sent(3):
                    logger.info(f"    ✓ Reminder 3 should be sent (target date passed, reminder 2 sent, reminder 3 never sent)")
                    return True, subscription_end_date
            else:
                days_diff = (target_date - today).days
                logger.debug(f"    Reminder 3: target date is {days_diff} days in the future")
            return False, None
        
        return False, None
    
    def log_message(self, member_id: int, subscription_id: Optional[int], reminder_number: int,
                   subscription_end_date: date, message: str, phone_number: str,
                   status: str, error_message: Optional[str] = None):
        """Log message to database"""
        if not self.db_connection or not self.db_connection.is_connected():
            return
        
        try:
            cursor = self.db_connection.cursor()
            query = """
                INSERT INTO trn_whatsapp_message_history
                (member_id, subscription_id, reminder_number, subscription_end_date,
                 message, phone_number, status, error_message, sent_at, created_at, updated_at)
                VALUES (%s, %s, %s, %s, %s, %s, %s, %s, NOW(), NOW(), NOW())
            """
            cursor.execute(query, (
                member_id, subscription_id, reminder_number, subscription_end_date,
                message, phone_number, status, error_message
            ))
            self.db_connection.commit()
            cursor.close()
            logger.info(f"Message logged for member {member_id}, reminder {reminder_number}")
            
        except Error as e:
            logger.error(f"Error logging message: {e}")
            self.db_connection.rollback()
    
    def process_member(self, member: Dict):
        """
        Process a single member and send appropriate reminders
        Handles the logic to avoid sending multiple messages for same member
        """
        # Get message history for this member's earliest subscription end date
        subscription_end_date = member['earliest_end_date']
        history = self.get_message_history(member['member_id'], subscription_end_date)
        
        logger.info(f"Processing member {member['member_id']} ({member['member_name']})")
        logger.info(f"  - Subscription end date: {subscription_end_date}")
        logger.info(f"  - Message history: {list(history.keys()) if history else 'None'}")
        
        # Check if member already received all 3 reminders successfully for this subscription end date
        # Only skip if all 3 reminders were successfully sent (status='sent')
        successfully_sent = [rem_num for rem_num in [1, 2, 3] 
                            if rem_num in history and history[rem_num].get('status') == 'sent']
        if len(successfully_sent) >= 3:
            logger.info(f"Member {member['member_id']} already received all 3 reminders successfully for end date {subscription_end_date}. Skipping.")
            return
        
        today = date.today()
        logger.info(f"  - Today's date: {today}")
        
        # Check each reminder level
        for reminder_number in [1, 2, 3]:
            should_send, target_end_date = self.should_send_reminder(member, reminder_number, history)
            logger.info(f"  - Reminder {reminder_number}: should_send={should_send}, target_end_date={target_end_date}")
            
            if should_send:
                # Get appropriate message template
                if reminder_number == 1:
                    template = self.settings['reminder_1_message']
                    days_ago = 0
                elif reminder_number == 2:
                    template = self.settings['reminder_2_message']
                    days_ago = self.settings['reminder_2_days']
                else:
                    template = self.settings['reminder_3_message']
                    days_ago = self.settings['reminder_2_days'] + self.settings['reminder_3_days']
                
                # Format message
                message = self.format_message(
                    template,
                    member['member_name'],
                    target_end_date,
                    days_ago
                )
                
                # Send message
                logger.info(f"Sending reminder {reminder_number} to {member['member_name']} ({member['phone_number']})")
                success, result_message = self.send_whatsapp_message(member['phone_number'], message)
                
                # Log message
                status = 'sent' if success else 'failed'
                error_msg = None if success else result_message
                self.log_message(
                    member['member_id'],
                    member.get('subscription_id'),
                    reminder_number,
                    target_end_date,
                    message,
                    member['phone_number'],
                    status,
                    error_msg
                )
                
                # Only send one reminder per member per run
                break
    
    def is_within_time_window(self) -> bool:
        """
        Check if current time is within the allowed time window for sending messages
        Returns True if current time is between start_time and end_time
        """
        try:
            current_time = datetime.now().time()
            
            # Parse start and end times
            start_hour, start_minute = map(int, self.settings['start_time'].split(':'))
            end_hour, end_minute = map(int, self.settings['end_time'].split(':'))
            
            start_time_obj = datetime.strptime(self.settings['start_time'], '%H:%M').time()
            end_time_obj = datetime.strptime(self.settings['end_time'], '%H:%M').time()
            
            # Check if current time is within the window
            if start_time_obj <= end_time_obj:
                # Normal case: start time is before end time (e.g., 09:00 to 21:00)
                return start_time_obj <= current_time <= end_time_obj
            else:
                # Edge case: time window spans midnight (e.g., 22:00 to 06:00)
                return current_time >= start_time_obj or current_time <= end_time_obj
                
        except Exception as e:
            logger.error(f"Error checking time window: {e}")
            # Default to allowing if there's an error parsing times
            return True
    
    def run(self):
        """Main execution method"""
        logger.info("=" * 80)
        logger.info("WhatsApp Automation Script Started (Twilio SDK)")
        logger.info("=" * 80)
        
        # Connect to database
        if not self.connect_to_database():
            logger.error("Failed to connect to database. Exiting.")
            return False
        
        try:
            # Load settings
            self.load_settings()
            
            # Check if automation is enabled
            if not self.settings['enabled']:
                logger.info("WhatsApp automation is disabled in settings. Exiting.")
                return True
            
            # Check if current time is within allowed time window
            if not self.is_within_time_window():
                current_time = datetime.now().strftime('%H:%M')
                logger.info(f"Current time {current_time} is outside allowed time window ({self.settings['start_time']} - {self.settings['end_time']}). Exiting.")
                return True
            
            # Validate API credentials
            if not self.settings['api_key'] or not self.settings['api_secret']:
                logger.warning("WhatsApp API credentials not configured. Exiting.")
                return False
            
            # Validate WhatsApp from number
            if not self.settings['from_number'] or not self.settings['from_number'].strip():
                logger.warning("WhatsApp from number not configured. Cannot send messages. Exiting.")
                return False
            
            # Initialize Twilio client
            if not self.initialize_twilio_client():
                logger.error("Failed to initialize Twilio client. Exiting.")
                return False
            
            # Get members needing reminders
            members = self.get_members_needing_reminders()
            
            if not members:
                logger.info("No members need reminders at this time.")
                return True
            
            # Process each member
            processed_count = 0
            sent_count = 0
            
            for member in members:
                try:
                    # Skip if member doesn't have valid phone number
                    # Pakistan phone numbers: 03XX-XXXXXXX (11 digits) or 3XX-XXXXXXX (10 digits)
                    phone_number = member['phone_number'].strip() if member['phone_number'] else ''
                    if not phone_number or len(phone_number) < 10:
                        logger.warning(f"Member {member['member_id']} ({member['member_name']}) has invalid phone number. Skipping.")
                        continue
                    
                    # Validate Pakistan phone number format (should start with 03 or 3)
                    if not (phone_number.startswith('03') or phone_number.startswith('3')):
                        logger.warning(f"Member {member['member_id']} ({member['member_name']}) phone number doesn't match Pakistan format (03XX-XXXXXXX). Skipping.")
                        continue
                    
                    self.process_member(member)
                    processed_count += 1
                    
                except Exception as e:
                    logger.error(f"Error processing member {member.get('member_id', 'unknown')}: {e}")
                    continue
            
            logger.info(f"Processed {processed_count} members")
            logger.info("=" * 80)
            logger.info("WhatsApp Automation Script Completed")
            logger.info("=" * 80)
            
            return True
            
        except Exception as e:
            logger.error(f"Unexpected error in run(): {e}", exc_info=True)
            return False
            
        finally:
            if self.db_connection and self.db_connection.is_connected():
                self.db_connection.close()
                logger.info("Database connection closed")


def main():
    """Main entry point"""
    automation = WhatsAppAutomation()
    success = automation.run()
    sys.exit(0 if success else 1)


if __name__ == "__main__":
    main()


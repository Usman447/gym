#!/bin/bash
# Setup script for Gymstation Cron Jobs and Services
# This script sets up both WhatsApp automation (cron) and Attendance monitor (systemd)

set -e

SCRIPT_DIR="/home/mint/gymstation/python_scripts"
PYTHON_PATH="$SCRIPT_DIR/venv/bin/python3"
LOGS_DIR="$SCRIPT_DIR/logs"

echo "=========================================="
echo "Gymstation Cron Jobs & Services Setup"
echo "=========================================="
echo ""

# Check if running as root for systemd setup
if [ "$EUID" -ne 0 ] && [ "$1" != "--skip-systemd" ]; then
    echo "⚠️  Note: Some commands require sudo. You may be prompted for your password."
    echo ""
fi

# Step 1: Create logs directory
echo "Step 1: Creating logs directory..."
mkdir -p "$LOGS_DIR"
echo "✓ Logs directory created: $LOGS_DIR"
echo ""

# Step 2: Make scripts executable
echo "Step 2: Making scripts executable..."
chmod +x "$SCRIPT_DIR/whatsapp_automation.py"
chmod +x "$SCRIPT_DIR/attendance_monitor.py"
echo "✓ Scripts are now executable"
echo ""

# Step 3: Setup WhatsApp Automation (Cron)
echo "Step 3: Setting up WhatsApp Automation (Cron)..."
# Check if already exists
if crontab -l 2>/dev/null | grep -q "whatsapp_automation.py"; then
    echo "⚠️  WhatsApp automation cron job already exists. Skipping..."
else
    (crontab -l 2>/dev/null; echo "*/30 * * * * cd $SCRIPT_DIR && $PYTHON_PATH whatsapp_automation.py >> $LOGS_DIR/whatsapp_cron.log 2>&1") | crontab -
    echo "✓ WhatsApp automation cron job added (runs every 30 minutes)"
fi
echo ""

# Step 4: Setup Attendance Monitor (systemd)
if [ "$1" != "--skip-systemd" ]; then
    echo "Step 4: Setting up Attendance Monitor (systemd service)..."
    
    # Create systemd service file
    SERVICE_FILE="/etc/systemd/system/attendance-monitor.service"
    
    if [ -f "$SERVICE_FILE" ]; then
        echo "⚠️  Service file already exists. Do you want to overwrite it? (y/n)"
        read -r response
        if [[ ! "$response" =~ ^[Yy]$ ]]; then
            echo "Skipping systemd service creation..."
        else
            sudo bash -c "cat > $SERVICE_FILE << EOF
[Unit]
Description=ZKTeco Attendance Monitor Service
After=network.target mysql.service

[Service]
Type=simple
User=mint
Group=mint
WorkingDirectory=$SCRIPT_DIR
ExecStart=$PYTHON_PATH $SCRIPT_DIR/attendance_monitor.py
Restart=always
RestartSec=10
StandardOutput=append:$LOGS_DIR/attendance_monitor.log
StandardError=append:$LOGS_DIR/attendance_monitor.log

[Install]
WantedBy=multi-user.target
EOF"
            echo "✓ Service file created"
            
            # Reload systemd and enable service
            sudo systemctl daemon-reload
            sudo systemctl enable attendance-monitor.service
            echo "✓ Service enabled (will start on boot)"
            
            # Check if service is already running
            if sudo systemctl is-active --quiet attendance-monitor.service; then
                echo "✓ Service is already running"
            else
                echo "Starting service..."
                sudo systemctl start attendance-monitor.service
                echo "✓ Service started"
            fi
        fi
    else
        sudo bash -c "cat > $SERVICE_FILE << EOF
[Unit]
Description=ZKTeco Attendance Monitor Service
After=network.target mysql.service

[Service]
Type=simple
User=mint
Group=mint
WorkingDirectory=$SCRIPT_DIR
ExecStart=$PYTHON_PATH $SCRIPT_DIR/attendance_monitor.py
Restart=always
RestartSec=10
StandardOutput=append:$LOGS_DIR/attendance_monitor.log
StandardError=append:$LOGS_DIR/attendance_monitor.log

[Install]
WantedBy=multi-user.target
EOF"
        echo "✓ Service file created"
        
        # Reload systemd and enable service
        sudo systemctl daemon-reload
        sudo systemctl enable attendance-monitor.service
        echo "✓ Service enabled (will start on boot)"
        
        # Start service
        sudo systemctl start attendance-monitor.service
        echo "✓ Service started"
    fi
    echo ""
else
    echo "Step 4: Skipping systemd setup (use --skip-systemd flag)"
    echo "⚠️  You'll need to manually set up attendance_monitor.py as a service"
    echo ""
fi

# Step 5: Verify setup
echo "=========================================="
echo "Verification"
echo "=========================================="
echo ""

echo "Cron Jobs:"
crontab -l | grep -E "(whatsapp|attendance)" || echo "  (none found)"
echo ""

if [ "$1" != "--skip-systemd" ]; then
    echo "Attendance Monitor Service Status:"
    sudo systemctl status attendance-monitor.service --no-pager -l || echo "  (service not found)"
    echo ""
fi

echo "=========================================="
echo "Setup Complete!"
echo "=========================================="
echo ""
echo "Useful Commands:"
echo ""
echo "View cron jobs:"
echo "  crontab -l"
echo ""
echo "View WhatsApp automation logs:"
echo "  tail -f $LOGS_DIR/whatsapp_cron.log"
echo ""

if [ "$1" != "--skip-systemd" ]; then
    echo "Attendance Monitor Service:"
    echo "  sudo systemctl status attendance-monitor"
    echo "  sudo systemctl start attendance-monitor"
    echo "  sudo systemctl stop attendance-monitor"
    echo "  sudo systemctl restart attendance-monitor"
    echo "  tail -f $LOGS_DIR/attendance_monitor.log"
    echo ""
fi

echo "Test scripts manually:"
echo "  cd $SCRIPT_DIR"
echo "  $PYTHON_PATH whatsapp_automation.py"
echo "  $PYTHON_PATH attendance_monitor.py  # (Ctrl+C to stop)"
echo ""


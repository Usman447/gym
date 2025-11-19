#!/bin/bash

# Change to the script directory using absolute path
cd /home/mint/gymstation/python_scripts

# Activate virtual environment
source /home/mint/gymstation/python_scripts/venv/bin/activate

# Run the Python script
python3 whatsapp_automation_twilio.py

# Deactivate is not necessary (script ends anyway)
deactivate

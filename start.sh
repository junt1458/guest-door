#!/bin/bash
SCRIPT_DIR=$(cd $(dirname $0); pwd)
/usr/bin/screen -dmS Guest-Door-Py /usr/bin/python $SCRIPT_DIR/main.py
echo "The program has been started."
echo "Type \"screen -r Guest-Door-Py\" to attach program output."
echo "Press Ctrl+A->D to detach from screen."
echo "Press Ctrl+C after attaching to quit application"

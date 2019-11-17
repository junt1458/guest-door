#!/bin/bash
SCRIPT_DIR=$(cd $(dirname $0); pwd)
/usr/bin/screen -dmS Guest-Door-Py /usr/bin/python $SCRIPT_DIR/main.py
/usr/bin/screen -S Guest-Door-Py -X multiuser on
/usr/bin/screen -S Guest-Door-Py -X acladd www-data
echo "The program has been started."
echo "Type \"screen -r Guest-Door-Py\" to attach program output."
echo "Press Ctrl+A->D to detach from screen."
echo "Press Ctrl+C after attaching to quit application"

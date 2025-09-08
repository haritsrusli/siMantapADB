import sys
import json

print(json.dumps({
    "script_name": sys.argv[0],
    "all_arguments": sys.argv,
    "argument_count": len(sys.argv)
}))

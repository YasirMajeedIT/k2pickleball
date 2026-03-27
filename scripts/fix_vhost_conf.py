# Fix vhost.conf: remove the broken context block appended at the bottom
import re

path = '/usr/local/lsws/conf/vhosts/k2pickleball.com/vhost.conf'
content = open(path).read()

# Remove the malformed block that was incorrectly appended
# It starts with "}context / {" (no newline between } and context)
# Everything from that point is garbage - strip it
fixed = re.sub(r'\}context / \{.*$', '}', content, flags=re.DOTALL)

# Ensure file ends cleanly with a newline
fixed = fixed.rstrip() + '\n'

open(path, 'w').write(fixed)
print("vhost.conf cleaned successfully")

# Verify the fix
result = open(path).read()
if 'context / {' in result:
    print("WARNING: broken block still present!")
else:
    print("Verified: no broken block remaining")
print("\nLast 10 lines of file:")
print('\n'.join(result.strip().split('\n')[-10:]))

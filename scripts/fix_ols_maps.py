# Fix two issues in httpd_config.conf:
# 1. Wildcard map arguments were reversed - fix the order
# 2. SSL listener uses wrong cert (srv779961 instead of k2pickleball.com)

path = '/usr/local/lsws/conf/httpd_config.conf'
content = open(path).read()

# --- Fix 1: Remove wrong wildcard lines (reversed order) ---
wrong = '  map                     *.k2pickleball.com k2pickleball.com\n'
content = content.replace(wrong, '')
print(f"Removed {3 - content.count('*.k2pickleball.com')} wrong wildcard lines" 
      if '*.k2pickleball.com' not in content else "Removed wrong wildcard lines")

# --- Fix 2: Add correct wildcard lines after each k2pickleball.com map ---
correct_map = '  map                     k2pickleball.com k2pickleball.com'
correct_with_wildcard = correct_map + '\n  map                     k2pickleball.com *.k2pickleball.com'

if '*.k2pickleball.com' not in content:
    content = content.replace(correct_map, correct_with_wildcard)
    print("Added correct wildcard map lines (vhost=k2pickleball.com, domain=*.k2pickleball.com)")
else:
    print("Wildcard map already present")

# --- Fix 3: Update SSL listener to use k2pickleball.com wildcard cert ---
old_key  = 'keyFile                  /etc/letsencrypt/live/srv779961.hstgr.cloud/privkey.pem'
old_cert = 'certFile                 /etc/letsencrypt/live/srv779961.hstgr.cloud/fullchain.pem'
new_key  = 'keyFile                  /etc/letsencrypt/live/k2pickleball.com/privkey.pem'
new_cert = 'certFile                 /etc/letsencrypt/live/k2pickleball.com/fullchain.pem'

if old_key in content:
    content = content.replace(old_key, new_key)
    content = content.replace(old_cert, new_cert)
    print("Fixed SSL listener cert to use k2pickleball.com wildcard cert")
else:
    print("SSL cert path already correct or different spacing - check manually")

open(path, 'w').write(content)
print("\nDone. Verify:")
for line in content.split('\n'):
    if 'k2pickleball' in line and ('map' in line or 'keyFile' in line or 'certFile' in line):
        print(' ', line)

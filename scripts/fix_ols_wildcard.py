content = open('/usr/local/lsws/conf/httpd_config.conf').read()
old = '  map                     k2pickleball.com k2pickleball.com'
new = old + '\n  map                     *.k2pickleball.com k2pickleball.com'
if '*.k2pickleball.com' not in content:
    content = content.replace(old, new)
    open('/usr/local/lsws/conf/httpd_config.conf', 'w').write(content)
    print('Done - added wildcard to all listeners')
else:
    print('Already exists - no changes made')

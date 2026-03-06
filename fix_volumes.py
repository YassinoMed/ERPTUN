import re

with open('docker-compose.yml', 'r') as f:
    data = f.read()

# Replace volumes mappings like "- ./Modules/Billing:/var/www/html" with "- ./:/var/www/html"
data = re.sub(r'volumes:\s*\[\s*"\./Modules/[^:]+:/var/www/html"\s*\]', 'volumes: [ "./:/var/www/html" ]', data)

# Same for context overrides
data = re.sub(r'context:\s*\./Modules/[a-zA-Z0-9_-]+', 'context: .', data)

with open('docker-compose.yml', 'w') as f:
    f.write(data)

print("docker-compose.yml updated successfully!")

#!/bin/bash

echo "=== Testing WhatsApp Server Connection ==="
echo ""

echo "1. Check Port 3000"
netstat -tlnp | grep 3000
echo ""

echo "2. Test Status Endpoint"
curl -v http://localhost:3000/status 2>&1
echo ""

echo "3. Test with 127.0.0.1"
curl -v http://127.0.0.1:3000/status 2>&1
echo ""

echo "4. Test Send Message"
curl -X POST http://localhost:3000/send \
  -H "Content-Type: application/json" \
  -d '{"phone":"628521634340","message":"Test from curl"}' 2>&1
echo ""

echo "5. Check Laravel .env WA_API_URL"
grep WA_API_URL /www/wwwroot/spmb/.env
echo ""

echo "Done!"

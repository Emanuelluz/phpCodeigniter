#!/bin/bash
echo "================================"
echo "Running CodeIgniter Migrations"
echo "================================"

# Wait for database to be ready
echo "Waiting for database..."
sleep 5

# Run migrations
php spark migrate --all

# Check if migrations succeeded
if [ $? -eq 0 ]; then
    echo "================================"
    echo "Migrations completed successfully!"
    echo "================================"
else
    echo "================================"
    echo "ERROR: Migrations failed!"
    echo "================================"
    exit 1
fi

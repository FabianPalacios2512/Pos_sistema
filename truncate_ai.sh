#!/bin/bash
mysql -u root -e "TRUNCATE TABLE tenantfabian.ai_usage_logs;"
mysql -u root -e "TRUNCATE TABLE tenantfabian2.ai_usage_logs;"
mysql -u root -e "TRUNCATE TABLE tenantpaleria_sofie.ai_usage_logs;"
mysql -u root -e "TRUNCATE TABLE tenanttes.ai_usage_logs;"
mysql -u root -e "TRUNCATE TABLE tenanttest.ai_usage_logs;"
echo "ALL AI USAGE LOGS TRUNCATED SUCCESSFULLY"

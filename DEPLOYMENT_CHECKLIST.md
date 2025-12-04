# 🚀 Deployment Checklist - Mercedes-Benz Bot Dashboard

## ✅ Pre-Deployment Verification (Completed)

### Application Setup
- ✅ Laravel 11.47.0 installed
- ✅ PHP 8.3.14 configured locally
- ✅ All migrations run successfully
- ✅ Twilio SDK installed (v8.8.7)
- ✅ All routes configured (API + Chat)
- ✅ ChatController implemented
- ✅ Agent chat system functional
- ✅ Database connection configured

### Critical Files Present
- ✅ `.nixpacks.toml` - PHP 8.3 configuration for Coolify
- ✅ `INTEGRATION_FLOW_GUIDE.md` - Twilio flow integration guide
- ✅ `AGENT_CHAT_SYSTEM.md` - Agent chat documentation
- ✅ `TWILIO_INTEGRATION_GUIDE.md` - Complete Twilio setup guide
- ✅ Migration: `2025_12_03_085052_add_agent_id_to_conversations_table.php`

---

## 🔧 Coolify Deployment Steps

### Step 1: Configure Environment Variables in Coolify

Navigate to your application in Coolify → **Environment Variables** tab.

#### ⚠️ CRITICAL: Build-Time Variables

Add these as **BUILD-TIME** environment variables:

```bash
NIXPACKS_PHP_VERSION=8.3
```

**Important:** This must be a BUILD-TIME variable, not runtime!

#### Runtime Environment Variables

Add/verify these as **RUNTIME** environment variables:

```bash
# Application
APP_NAME="Mercedes-Benz Bot Dashboard"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://mbbot-dashboard.ywcdigital.com

# Database (your existing production DB)
DB_CONNECTION=mysql
DB_HOST=142.93.236.118
DB_PORT=3309
DB_DATABASE=mercedesbot
DB_USERNAME=mercedesbduser
DB_PASSWORD=KPeeICwVGGU9m2zPcsLhGcvEakDEt3e69RBksHCzcuZ7GPbeXxNDXEDVpyGgutRu

# Session & Cache
SESSION_DRIVER=database
SESSION_LIFETIME=120
CACHE_DRIVER=file
QUEUE_CONNECTION=sync

# Twilio Configuration
TWILIO_ACCOUNT_SID=your_twilio_account_sid
TWILIO_AUTH_TOKEN=your_twilio_auth_token
TWILIO_WHATSAPP_NUMBER=+2250716700900
TWILIO_PHONE_NUMBER=+2250716700900

# Security
WEBHOOK_AUTH_TOKEN=your-secret-token-here
SANCTUM_STATEFUL_DOMAINS=mbbot-dashboard.ywcdigital.com
```

**Note:** Generate a new `APP_KEY` with: `php artisan key:generate --show`

---

### Step 2: Verify .nixpacks.toml

Ensure this file is committed to your repository:

```toml
[phases.setup]
nixPkgs = ['php83', 'php83Packages.composer', 'nodejs_22', 'nginx']

[phases.install]
cmds = ['npm install', 'composer install --no-dev --optimize-autoloader --no-interaction']

[phases.build]
cmds = ['npm run build', 'php artisan optimize:clear']

[start]
cmd = 'php artisan serve --host=0.0.0.0 --port=3000'
```

---

### Step 3: Commit and Push Changes

```bash
git add .
git commit -m "Configure deployment for Coolify with PHP 8.3"
git push origin main
```

---

### Step 4: Deploy in Coolify

1. Navigate to your application in Coolify
2. Click **Deploy** button
3. Monitor the build logs
4. Wait for deployment to complete

Expected build output:
```
✓ Installing PHP 8.3
✓ Installing Composer dependencies
✓ Running npm install
✓ Building assets (npm run build)
✓ Clearing Laravel cache
✓ Starting application on port 3000
```

---

### Step 5: Post-Deployment Tasks

Once deployed, run these commands via Coolify's shell or SSH:

```bash
# Generate application key (if not set in env)
php artisan key:generate

# Run migrations
php artisan migrate --force

# Clear all caches
php artisan optimize:clear

# Cache configuration for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

### Step 6: Verify Deployment

Visit these URLs to verify everything works:

1. **Dashboard Homepage**
   - URL: https://mbbot-dashboard.ywcdigital.com
   - Expected: Login page or dashboard

2. **API Health Check**
   - URL: https://mbbot-dashboard.ywcdigital.com/api/twilio/incoming
   - Method: POST
   - Expected: JSON response (may require authentication)

3. **Test Authentication**
   - Login with your admin credentials
   - Navigate to: https://mbbot-dashboard.ywcdigital.com/dashboard/conversations
   - Expected: Conversations list

---

### Step 7: Configure Twilio Webhook

Update your Twilio Studio Flow webhook URL:

1. Go to Twilio Console → Studio → Your Flow
2. Update the `send_to_laravel_incoming` widget URL to:
   ```
   https://mbbot-dashboard.ywcdigital.com/api/twilio/incoming
   ```

3. Update all other webhook URLs in your flow:
   - `/api/twilio/menu-choice`
   - `/api/twilio/free-input`
   - `/api/twilio/agent-transfer`
   - `/api/twilio/complete`

4. **Publish** your Twilio Flow

---

## 🐛 Troubleshooting

### Issue: Build fails with "PHP version does not satisfy requirement"

**Solution:**
1. Verify `NIXPACKS_PHP_VERSION=8.3` is set as **BUILD-TIME** variable
2. OR ensure `.nixpacks.toml` is committed to repository
3. Redeploy

### Issue: Database connection error

**Solution:**
1. Verify database credentials in environment variables
2. Check if Coolify can reach `142.93.236.118:3309`
3. Test connection: `php artisan db:show`

### Issue: 500 Error on homepage

**Solution:**
1. Check storage permissions: `chmod -R 775 storage bootstrap/cache`
2. Generate APP_KEY: `php artisan key:generate`
3. Clear cache: `php artisan optimize:clear`
4. Check logs: `storage/logs/laravel.log`

### Issue: Twilio webhooks not working

**Solution:**
1. Verify URL in Twilio Studio matches deployed domain
2. Check webhook logs in Twilio Console → Monitor → Logs
3. Test endpoint manually with Postman/curl
4. Verify `TWILIO_ACCOUNT_SID` and `TWILIO_AUTH_TOKEN` in .env

### Issue: Assets not loading (CSS/JS)

**Solution:**
1. Verify `npm run build` ran during deployment
2. Check `public/build` directory exists
3. Run manually: `npm run build`
4. Clear view cache: `php artisan view:clear`

---

## 📊 Post-Deployment Verification Checklist

- [ ] Application loads at https://mbbot-dashboard.ywcdigital.com
- [ ] Login page accessible
- [ ] Can authenticate as admin
- [ ] Dashboard displays correctly
- [ ] Conversations page loads
- [ ] Agent chat interface accessible
- [ ] Twilio webhooks respond with 200 OK
- [ ] Test WhatsApp message creates conversation
- [ ] Agent can take over conversation
- [ ] Agent can send messages to client
- [ ] Database is recording all interactions

---

## 🔐 Security Recommendations

After successful deployment:

1. **Change default passwords**
   - Update admin user password
   - Generate new `WEBHOOK_AUTH_TOKEN`

2. **Configure HTTPS**
   - Coolify should handle this automatically
   - Verify SSL certificate is active

3. **Set APP_DEBUG=false**
   - Already in the environment variables above
   - Prevents sensitive error information exposure

4. **Restrict database access**
   - Consider firewall rules for database server
   - Only allow Coolify server IP

5. **Monitor logs regularly**
   - Check `storage/logs/laravel.log`
   - Monitor Twilio webhook logs

---

## 📞 Next Steps After Deployment

1. **Integrate Twilio Flow**
   - Follow `INTEGRATION_FLOW_GUIDE.md`
   - Add HTTP widgets to existing flow
   - Test each integration point

2. **Train agents**
   - Share `AGENT_CHAT_SYSTEM.md` with team
   - Demonstrate chat interface
   - Test agent takeover workflow

3. **Monitor conversations**
   - Check dashboard daily
   - Review conversation statistics
   - Identify common user requests

4. **Optimize performance**
   - Consider Redis for caching
   - Set up queue workers if needed
   - Monitor database query performance

---

## 📚 Documentation Reference

- **Twilio Integration:** `TWILIO_INTEGRATION_GUIDE.md`
- **Flow Integration:** `INTEGRATION_FLOW_GUIDE.md`
- **Agent Chat System:** `AGENT_CHAT_SYSTEM.md`
- **Coolify Logs:** Check Coolify dashboard for real-time logs

---

## ✅ Current Status

**Last Updated:** 2025-12-04
**Application Version:** Laravel 11.47.0
**PHP Version Required:** 8.3+
**Database:** MySQL (Remote)
**Deployment Platform:** Coolify
**Domain:** https://mbbot-dashboard.ywcdigital.com

---

**Ready for deployment!** Follow the steps above in order.

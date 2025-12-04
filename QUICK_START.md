# 🚀 Quick Start - Deploy in 3 Steps

## Your application is ready! Follow these 3 simple steps to deploy.

---

## Step 1: Configure Coolify (5 minutes)

### A. Add Build-Time Variable
1. Open Coolify Dashboard
2. Go to your application
3. Click **Environment Variables** tab
4. Click **Add** button
5. Enter:
   - **Name:** `NIXPACKS_PHP_VERSION`
   - **Value:** `8.3`
   - **Type:** Select **Build-time** ⚠️ (IMPORTANT!)
6. Click **Save**

### B. Verify Runtime Variables
Make sure these are set as **Runtime** variables:

```bash
APP_ENV=production
APP_DEBUG=false
APP_URL=https://mbbot-dashboard.ywcdigital.com

TWILIO_ACCOUNT_SID=your_twilio_account_sid
TWILIO_AUTH_TOKEN=your_twilio_auth_token
TWILIO_WHATSAPP_NUMBER=+2250716700900

DB_HOST=142.93.236.118
DB_PORT=3309
DB_DATABASE=mercedesbot
DB_USERNAME=mercedesbduser
DB_PASSWORD=KPeeICwVGGU9m2zPcsLhGcvEakDEt3e69RBksHCzcuZ7GPbeXxNDXEDVpyGgutRu
```

---

## Step 2: Deploy Application (10 minutes)

### A. Commit Changes
```bash
git add .
git commit -m "Configure for Coolify deployment with PHP 8.3"
git push origin main
```

### B. Deploy in Coolify
1. Go to Coolify Dashboard
2. Click **Deploy** button
3. Watch the build logs
4. Wait for "Deployment successful"

### C. Run Post-Deployment Commands
Once deployed, access the shell in Coolify and run:

```bash
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## Step 3: Test & Verify (5 minutes)

### Test the Application
1. Visit: https://mbbot-dashboard.ywcdigital.com
2. Log in with admin credentials
3. Go to Conversations page
4. Send test WhatsApp message to your Twilio number
5. Verify conversation appears in dashboard

---

## ✅ That's It!

Your application is now live and functional.

### Next Actions:

1. **Integrate your Twilio Flow** (see `INTEGRATION_FLOW_GUIDE.md`)
   - Add HTTP widgets to your existing flow
   - Point webhooks to your deployed URL
   - Test the complete flow

2. **Train your agents** (see `AGENT_CHAT_SYSTEM.md`)
   - Show them how to take over conversations
   - Demonstrate the chat interface
   - Practice sending messages

---

## 🆘 Need Help?

- **Deployment issues?** → `DEPLOYMENT_CHECKLIST.md`
- **Twilio problems?** → `TWILIO_INTEGRATION_GUIDE.md`
- **Flow integration?** → `INTEGRATION_FLOW_GUIDE.md`
- **Agent chat?** → `AGENT_CHAT_SYSTEM.md`

---

## 📞 Support Checklist

If something doesn't work:

1. ✅ Check Coolify build logs
2. ✅ Verify all environment variables are set
3. ✅ Check `NIXPACKS_PHP_VERSION=8.3` is **Build-time**
4. ✅ Check Laravel logs in Coolify: `storage/logs/laravel.log`
5. ✅ Verify database connection from Coolify server
6. ✅ Test Twilio credentials with `php artisan tinker`

---

**Ready? Start with Step 1! 🎯**

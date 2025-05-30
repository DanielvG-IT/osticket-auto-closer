# 🕒 osticket-auto-closer

Automatically manage your osTicket workflow by seamlessly transitioning tickets from **"Resolved"** to **"Closed"** status after a configurable waiting period (default: 7 days). This ensures tickets can't be reopened by end-users, keeping your support queue clean and efficient.

---

## 🛠️ How It Works

1. **Resolve a Ticket:**  
   When your support team marks a ticket as "Resolved", it enters a holding period.
2. **Wait Period:**  
   The ticket remains in "Resolved" status for the configured duration (e.g., 7 days).
3. **Auto-Close:**  
   After the waiting period, the script automatically changes the ticket status to "Closed".
4. **No More Reopens:**  
   With the appropriate osTicket setting enabled, end-users can no longer reopen these tickets.

---

## ⚙️ Setup

1. **Install the Plugin:**  
   Download the **osticket-auto-closer** plugin and place it in your osTicket `/include/plugins` directory.  
   In the osTicket Admin Panel, navigate to **Admin Panel → Manage → Plugins**, then click **Add New Plugin** and select **osticket-auto-closer** to install and enable it.

2. **Enable "Disallow Reopen Closed Tickets":**  
   In osTicket Admin Panel, go to **Admin Panel → Manage → List → Ticket Statuses** and enable **"Disallow Reopen Closed Tickets"**.

3. **Configure the Plugin:**  
   Set your desired waiting period and connect the plugin to your osTicket instance using the provided configuration options.

---

## 💡 Why Use This?

- **Reduces Ticket Clutter:**  
  Keeps your ticket queue focused on active issues.
- **Improves Workflow:**  
  Automates repetitive tasks for your support team.
- **Enhances User Experience:**  
  Provides clear ticket lifecycle management for end-users.

---

Make your osTicket support process smarter and more efficient with **osticket-auto-closer**!

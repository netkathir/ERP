# Quick Setup: Approval System

## ✅ YES, THERE IS A MASTER TO CONFIGURE THE APPROVAL FLOW!

## Step 1: Access Approval Masters

**Option A: Via Sidebar Menu**
1. Login as Super Admin
2. Look in the sidebar under **"Tender Sales"** section
3. Click on **"Approval Masters"**

**Option B: Direct URL**
- Go to: `http://your-domain/approval-masters`
- Or: `http://127.0.0.1:8000/approval-masters`

## Step 2: Create Approval Master for Customer Orders

1. Click **"Create Approval Master"** button
2. Fill in the form:
   ```
   Form Name: customer_orders
   Display Name: Customer Orders
   Description: (optional) Approval for Customer Orders
   Active: ✓ (check this box)
   ```
3. **Select Approvers**: Check the boxes next to users who should approve orders
4. Click **"Create Approval Master"**

## Step 3: Verify Setup

1. Go back to Approval Masters list
2. You should see "Customer Orders" in the list
3. Check that approvers are listed

## Step 4: Test the Flow

1. Create a new Customer Order
2. Go to: **"Pending Approvals"** (in sidebar under Tender Sales)
3. Or: `http://127.0.0.1:8000/approvals?form=customer_orders`
4. You should see the pending order

## Step 5: Approve/Reject

1. Click "View" on a pending order
2. Click "Approve" or "Reject"
3. Add remarks if needed
4. Submit

## Troubleshooting

### Can't see "Approval Masters" menu?
- Make sure you're logged in as **Super Admin**
- Check if the menu item is visible in sidebar under "Tender Sales"

### Getting 403 error?
- You need Super Admin access
- Or permission: `approvals` with `view` action

### No data in Approval Masters?
- This is normal if you haven't created any yet
- Click "Create Approval Master" to add one

### Notifications not coming?
- Make sure Approval Master is created
- Make sure approvers are selected
- Check Laravel logs: `storage/logs/laravel.log`

## Menu Locations

- **Approval Masters**: Sidebar → Tender Sales → Approval Masters
- **Pending Approvals**: Sidebar → Tender Sales → Pending Approvals


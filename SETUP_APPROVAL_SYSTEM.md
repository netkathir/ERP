# Approval System Setup Guide

## Step 1: Create Approval Master

1. Go to: `/approval-masters/create`
2. Fill in the form:
   - **Form Name**: `customer_orders` (must be exactly this)
   - **Display Name**: `Customer Orders`
   - **Description**: (optional) Description of what this approval is for
   - **Active**: Check this box
   - **Approvers**: Select one or more users who should receive approval notifications

3. Click "Create Approval Master"

## Step 2: Test the Flow

1. Create a new Customer Order
2. After creation, the system will:
   - Set the order status to "pending"
   - Send notifications to all configured approvers
   - Log the notification sending in Laravel logs

## Step 3: View Pending Approvals

1. Go to: `/approvals?form=customer_orders`
2. Or use the "Pending Approvals" menu item in the sidebar (under Tender Sales)
3. You'll see all pending Customer Orders that need approval

## Step 4: Approve or Reject

1. Click "View" to see the order details
2. Click "Approve" to approve the order
3. Click "Reject" to reject (requires remarks)

## Troubleshooting

### Notifications Not Coming?

1. **Check if Approval Master exists:**
   - Go to `/approval-masters`
   - Make sure there's an entry for "customer_orders"
   - Make sure it's "Active"

2. **Check if Approvers are configured:**
   - Edit the approval master
   - Make sure at least one user is selected as an approver

3. **Check Laravel logs:**
   - Look in `storage/logs/laravel.log`
   - Search for "Approval notification" to see what's happening

4. **Check database:**
   - Verify `approval_masters` table exists
   - Verify `approval_mappings` table exists
   - Verify `notifications` table exists

### Approval Flow Not Working?

1. Make sure the approval master is set up (Step 1)
2. Check that the user creating the order has permission
3. Verify the notification is being sent (check logs)
4. Make sure approvers can access `/approvals` route

## Adding More Forms

To add approval for other forms (e.g., Production Orders):

1. Create approval master with form_name: `production_orders`
2. Update `ApprovalController::getPendingRecords()` to handle the new form
3. Update `ApprovalController::getRecord()` to handle the new form
4. Add approval fields to the new form's table
5. Use `HasApprovalStatus` trait in the model
6. Send notifications after creating records


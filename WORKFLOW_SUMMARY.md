# Super Admin & Organization Admin Workflows

## 🔐 Super Admin Workflow

### Responsibilities
- **Create Organizations**: Create new organizations and assign the first Organization Admin at creation time
- **View All Organizations**: Access to view all organizations in the system
- **Manage Organization Admins**: Can only view and manage Organization Admin users (not regular users within organizations)
- **System Configuration**: Manage high-level system configurations
- **Organization Switching**: Switch between organizations for reporting and oversight

### Restrictions
- ❌ **Cannot manage users within organizations** (except Organization Admins)
- ❌ **Cannot create regular users** (Branch Admin, Branch Users, etc.)
- ❌ **Cannot directly manage branches** within organizations
- ✅ **Can only manage Organization Admins** - these are the users assigned when creating organizations

### Access Control
- **Users Menu**: Shows only Organization Admins
- **Organizations Menu**: Full access to create, view, edit, and delete organizations
- **Branches Menu**: Can view all branches for oversight, but cannot manage them
- **Roles/Permissions Menu**: Not accessible (Organization Admin manages these)

### Organization Switching
Super Admin can switch to a specific organization view for reporting:
- Click "Switch" button on any organization
- View organization-specific data
- Click "View All Organizations" to return to full view

---

## 🏢 Organization Admin Workflow

### Responsibilities
- **Full Autonomy**: Complete control over their organization
- **User Management**: Create and manage all users within their organization
  - Branch Admins
  - Branch Users
  - Any role except Super Admin
- **Role & Permission Management**: Assign roles and permissions to users within their organization
- **Branch Management**: Create and manage branches within their organization
- **User Assignment**: Assign users to specific branches

### Capabilities
- ✅ **Create Users**: Can create any user type (except Super Admin) within their organization
- ✅ **Assign Roles**: Can assign any role (except Super Admin) to users
- ✅ **Manage Permissions**: Full access to manage permissions and assign them to roles
- ✅ **Manage Branches**: Create, edit, and delete branches within their organization
- ✅ **Assign Users to Branches**: Assign users to specific branches

### Access Control
- **Users Menu**: Shows all users within their organization
- **Branches Menu**: Shows and manages branches within their organization
- **Roles Menu**: Full access to manage roles
- **Permissions Menu**: Full access to manage permissions
- **Organizations Menu**: Not accessible (Super Admin manages organizations)

### First Login
When an Organization Admin is created by Super Admin:
1. Organization Admin receives email with default password: `OrgAdmin@[Year]`
2. Organization Admin logs in and should change password immediately
3. Organization Admin can start creating users, branches, and managing their organization

---

## 🔄 Workflow Summary

### Super Admin Creates Organization
1. Super Admin creates organization
2. Provides Organization Admin email
3. System automatically creates Organization Admin user
4. Organization Admin receives default password

### Organization Admin Manages Organization
1. Organization Admin logs in
2. Creates branches within their organization
3. Creates users (Branch Admins, Users, etc.)
4. Assigns users to branches
5. Manages roles and permissions
6. Full autonomy - no Super Admin involvement needed

### Super Admin Oversight
1. Super Admin can view all organizations
2. Can switch to specific organization for reporting
3. Can view Organization Admins only
4. Cannot interfere with day-to-day operations

---

## 📋 Menu Access Summary

| Menu Item | Super Admin | Organization Admin | Branch Admin |
|-----------|-------------|-------------------|--------------|
| Dashboard | ✅ | ✅ | ✅ |
| Users | ✅ (Org Admins only) | ✅ (All in org) | ✅ (Branch only) |
| Roles | ❌ | ✅ | ❌ |
| Permissions | ❌ | ✅ | ❌ |
| Organizations | ✅ | ❌ | ❌ |
| Branches | ✅ (View only) | ✅ (Manage) | ✅ (View own) |
| Settings | ✅ | ✅ | ✅ |

---

## 🎯 Key Points

1. **Super Admin** = Organization-level management only
2. **Organization Admin** = Full autonomy within their organization
3. **No Overlap**: Super Admin cannot manage users within organizations
4. **Clear Separation**: Each Organization Admin has complete control over their organization
5. **Reporting**: Super Admin can switch between organizations for oversight


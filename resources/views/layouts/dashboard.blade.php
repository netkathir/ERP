<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard - ERP System')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <!-- Styles -->
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Figtree', sans-serif;
            background: #f5f5f5;
        }
        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            width: 250px;
            background: #2c3e50;
            color: white;
            position: fixed;
            height: 100vh;
            left: 0;
            top: 0;
            transition: all 0.3s ease;
            z-index: 1000;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        .sidebar.collapsed {
            width: 70px;
        }
        .sidebar.closed {
            transform: translateX(-100%);
        }
        .sidebar-header {
            padding: 18px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            min-height: 60px;
        }
        .sidebar.collapsed .sidebar-header {
            justify-content: center;
            padding: 18px 0;
        }
        .logo {
            font-size: 18px;
            font-weight: 600;
            white-space: nowrap;
            opacity: 1;
            transition: opacity 0.3s;
            flex: 1;
            line-height: 1.2;
            display: flex;
            align-items: center;
        }
        .sidebar.collapsed .logo {
            opacity: 0;
            width: 0;
            overflow: hidden;
        }
        .menu-toggle {
            background: none;
            border: none;
            color: #ffffff !important;
            font-size: 20px;
            cursor: pointer;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 5px;
            transition: all 0.3s;
            flex-shrink: 0;
            margin-left: 10px;
        }
        .sidebar.collapsed .menu-toggle {
            margin-left: 0;
            width: 100%;
            justify-content: center;
        }
        .menu-toggle:hover {
            background: rgba(255,255,255,0.1);
        }
        .menu-toggle i {
            color: #ffffff !important;
            display: block;
            line-height: 1;
        }
        .sidebar-menu {
            padding: 8px 0;
            overflow-y: auto;
            overflow-x: hidden;
            flex: 1;
            max-height: calc(100vh - 60px);
        }
        .sidebar-menu::-webkit-scrollbar {
            width: 6px;
        }
        .sidebar-menu::-webkit-scrollbar-track {
            background: rgba(255,255,255,0.05);
        }
        .sidebar-menu::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.2);
            border-radius: 3px;
        }
        .sidebar-menu::-webkit-scrollbar-thumb:hover {
            background: rgba(255,255,255,0.3);
        }

        /* Simple inline loader for submit buttons */
        .btn-loading-spinner {
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top-color: #ffffff;
            border-radius: 50%;
            width: 14px;
            height: 14px;
            margin-right: 6px;
            display: inline-block;
            vertical-align: middle;
            animation: btn-spin 0.6s linear infinite;
        }

        @keyframes btn-spin {
            to { transform: rotate(360deg); }
        }
        .menu-item-header {
            padding: 12px 20px;
            font-size: 13px;
            color: #f9fafb;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
            user-select: none;
            transition: background 0.3s, color 0.3s;
            background: rgba(255,255,255,0.03);
        }
        .menu-item-header:hover {
            background: rgba(255,255,255,0.12);
            color: #ffffff;
        }
        .menu-item-header .menu-header-icon {
            font-size: 16px;
            margin-right: 8px;
        }
        .menu-item-header .arrow {
            transition: transform 0.3s ease;
            font-size: 10px;
            margin-left: 8px;
        }
        .menu-item-header.collapsed .arrow {
            transform: rotate(-90deg);
        }
        .menu-sub-items {
            overflow: hidden;
            transition: max-height 0.3s ease;
            max-height: 1000px;
        }
        .menu-sub-items.collapsed {
            max-height: 0;
        }
        .menu-item {
            padding: 14px 20px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 15px;
            transition: all 0.3s;
            position: relative;
            line-height: 1.5;
        }
        .menu-item:hover {
            background: rgba(255,255,255,0.1);
            color: white;
        }
        .menu-item i {
            width: 20px;
            text-align: left;
            font-size: 18px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: flex-start;
        }
        .menu-item span {
            white-space: nowrap;
            opacity: 1;
            transition: opacity 0.3s;
            line-height: 1.5;
            display: flex;
            align-items: center;
        }
        .sidebar.collapsed .menu-item span {
            opacity: 0;
            width: 0;
            overflow: hidden;
        }
        .sidebar.collapsed .menu-item {
            justify-content: center;
            padding: 14px 0;
            gap: 0;
        }
        /* In collapsed mode: show only the section icon, hide text and arrow */
        .sidebar.collapsed .menu-item-header span {
            display: none;
        }
        .sidebar.collapsed .menu-item-header .arrow {
            display: none;
        }
        .sidebar.collapsed .menu-item-header {
            justify-content: center;
        }
        .sidebar.collapsed .menu-item i {
            justify-content: center;
            text-align: center;
            width: 20px;
            margin: 0 auto;
        }
        .main-content {
            margin-left: 250px;
            flex: 1;
            transition: margin-left 0.3s ease;
        }
        .main-content.expanded {
            margin-left: 0;
        }
        .main-content.sidebar-collapsed {
            margin-left: 70px;
        }
        .top-header {
            background: #2c3e50;
            padding: 15px 30px;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .role-badge {
            background: #667eea;
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
        }
        .entity-badge {
            background: #48bb78;
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
        }
        .top-header .user-info {
            color: white;
        }
        .logout-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
            box-shadow: 0 2px 8px rgba(220, 53, 69, 0.3);
        }
        .logout-btn:hover {
            background: #c82333;
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.4);
            transform: translateY(-1px);
        }
        .logout-btn:active {
            transform: translateY(0);
            box-shadow: 0 2px 6px rgba(220, 53, 69, 0.3);
        }
        .logout-btn i {
            font-size: 16px;
        }
        .content-area {
            padding: 30px;
        }
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.open {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="logo">ERP System</div>
                <button class="menu-toggle" onclick="toggleSidebar()" title="Toggle Sidebar">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
            <nav class="sidebar-menu">
                <a href="{{ route('dashboard') }}" class="menu-item" title="Dashboard">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
                
                {{-- Account Settings --}}
                <a href="{{ route('account.change-password') }}" class="menu-item" title="Change Password">
                    <i class="fas fa-user-cog"></i>
                    <span>Account Settings</span>
                </a>
                
                {{-- System Admin Menu (Super Admin only) --}}
                @if(auth()->user()->isSuperAdmin())
                    <div class="menu-item-header" onclick="toggleSystemAdminMenu()" id="systemAdminHeader" style="margin-top: 10px;" title="System Admin">
                        <i class="fas fa-tools menu-header-icon"></i>
                        <span>System Admin</span>
                        <i class="fas fa-chevron-down arrow"></i>
                    </div>
                    <div class="menu-sub-items" id="systemAdminMenu">
                        <a href="{{ route('branches.index') }}" class="menu-item" title="Branches">
                            <i class="fas fa-sitemap"></i>
                            <span>Branches</span>
                        </a>
                        
                        <a href="{{ route('users.index') }}" class="menu-item" title="Users">
                            <i class="fas fa-users"></i>
                            <span>Users</span>
                        </a>
                        
                        <a href="{{ route('roles.index') }}" class="menu-item" title="Roles">
                            <i class="fas fa-user-shield"></i>
                            <span>Roles</span>
                        </a>
                        
                        <a href="{{ route('role-permissions.select') }}" class="menu-item" title="Role Permissions">
                            <i class="fas fa-key"></i>
                            <span>Role Permissions</span>
                        </a>
                    </div>
                @endif
                
                {{-- Branch User Menu --}}
                @if(auth()->user()->isBranchUser())
                    <a href="{{ route('transactions.index') }}" class="menu-item" title="Transactions">
                        <i class="fas fa-exchange-alt"></i>
                        <span>Transactions</span>
                    </a>
                @endif
                
                {{-- Enquiry Sales Module --}}
                <div class="menu-item-header" onclick="toggleEnquirySalesMenu()" id="enquirySalesHeader" style="margin-top: 10px;" title="Enquiry Sales">
                    <i class="fas fa-question-circle menu-header-icon"></i>
                    <span>Enquiry Sales</span>
                    <i class="fas fa-chevron-down arrow"></i>
                </div>
                <div class="menu-sub-items" id="enquirySalesMenu">
                <a href="{{ route('quotations.index') }}" class="menu-item" title="Quotations">
                    <i class="fas fa-file-invoice-dollar"></i>
                    <span>Quotations</span>
                </a>
                <a href="{{ route('proforma-invoices.index') }}" class="menu-item" title="Proforma Invoices">
                    <i class="fas fa-file-invoice"></i>
                    <span>Proforma Invoices</span>
                </a>
                </div>

                {{-- Tender Sales Module --}}
                @if(auth()->user()->isSuperAdmin() || auth()->user()->hasPermission('tenders', 'view'))
                    <div class="menu-item-header" onclick="toggleTenderSalesMenu()" id="tenderSalesHeader" style="margin-top: 10px;" title="Tender Sales">
                        <i class="fas fa-file-contract menu-header-icon"></i>
                        <span>Tender Sales</span>
                        <i class="fas fa-chevron-down arrow"></i>
                    </div>
                    <div class="menu-sub-items" id="tenderSalesMenu">
                        <a href="{{ route('tenders.index') }}" class="menu-item" title="Tenders">
                            <i class="fas fa-gavel"></i>
                            <span>Tenders</span>
                        </a>
                        <a href="{{ route('customer-orders.index') }}" class="menu-item" title="Customer Orders">
                            <i class="fas fa-file-contract"></i>
                            <span>Customer Orders</span>
                        </a>
                        <a href="{{ route('tender-evaluations.index') }}" class="menu-item" title="Tender Evaluation">
                            <i class="fas fa-clipboard-check"></i>
                            <span>Tender Evaluation</span>
                        </a>
                        @if(auth()->user()->isSuperAdmin() || auth()->user()->hasPermission('approvals', 'view'))
                        <a href="{{ route('approvals.index', ['form' => 'customer_orders']) }}" class="menu-item" title="Pending Approvals">
                            <i class="fas fa-check-circle"></i>
                            <span>Pending Approvals</span>
                        </a>
                        @endif
                        <a href="{{ route('customer-complaints.index') }}" class="menu-item" title="Customer Complaint Register">
                            <i class="fas fa-exclamation-circle"></i>
                            <span>Customer Complaint Register</span>
                        </a>
                    </div>
                @endif

                {{-- Supplier Master Module --}}
                @if(auth()->user()->isSuperAdmin() || auth()->user()->hasPermission('suppliers', 'view'))
                    <div class="menu-item-header" onclick="toggleSupplierMenu()" id="supplierHeader" style="margin-top: 10px;" title="Supplier">
                        <i class="fas fa-truck menu-header-icon"></i>
                        <span>Supplier</span>
                        <i class="fas fa-chevron-down arrow"></i>
                    </div>
                    <div class="menu-sub-items" id="supplierMenu">
                        <a href="{{ route('suppliers.index') }}" class="menu-item" title="Suppliers">
                            <i class="fas fa-truck"></i>
                            <span>Suppliers</span>
                        </a>
                        <a href="{{ route('supplier-evaluations.index') }}" class="menu-item" title="Supplier Evaluation">
                            <i class="fas fa-clipboard-check"></i>
                            <span>Supplier Evaluation</span>
                        </a>
                        <a href="{{ route('subcontractor-evaluations.index') }}" class="menu-item" title="Subcontractor Evaluation">
                            <i class="fas fa-clipboard-list"></i>
                            <span>Subcontractor Evaluation</span>
                        </a>
                    </div>
                @endif

                {{-- Purchase Module --}}
                 <div class="menu-item-header" onclick="togglePurchaseMenu()" id="purchaseHeader" style="margin-top: 10px;" title="Purchase">
                     <i class="fas fa-shopping-cart menu-header-icon"></i>
                    <span>Purchase</span>
                    <i class="fas fa-chevron-down arrow"></i>
                </div>
                <div class="menu-sub-items" id="purchaseMenu">
                    <a href="{{ route('purchase-indents.index') }}" class="menu-item" title="Purchase Indents">
                        <i class="fas fa-file-alt"></i>
                        <span>Purchase Indents</span>
                    </a>
                    <a href="{{ route('purchase-orders.index') }}" class="menu-item" title="Purchase Orders">
                        <i class="fas fa-shopping-cart"></i>
                        <span>Purchase Orders</span>
                    </a>
                </div>

                {{-- Store Module --}}
                 <div class="menu-item-header" onclick="toggleStoreMenu()" id="storeHeader" style="margin-top: 10px;" title="Store">
                     <i class="fas fa-warehouse menu-header-icon"></i>
                     <span>Store</span>
                     <i class="fas fa-chevron-down arrow"></i>
                 </div>
                <div class="menu-sub-items" id="storeMenu">
                    @if(auth()->user()->isSuperAdmin() || auth()->user()->hasPermission('material-inwards', 'view'))
                    <a href="{{ route('material-inwards.index') }}" class="menu-item" title="Material Inward">
                        <i class="fas fa-arrow-down"></i>
                        <span>Material Inward</span>
                    </a>
                    @endif
                </div>

                 <div class="menu-item-header" on
                 click="toggleMastersMenu()" id="mastersHeader" title="Masters">
                     <i class="fas fa-database menu-header-icon"></i>
                    <span>Masters</span>
                    <i class="fas fa-chevron-down arrow"></i>
                </div>
                <div class="menu-sub-items" id="mastersMenu">
                    @if(auth()->user()->hasPermission('departments', 'view') || auth()->user()->isSuperAdmin())
                    <a href="{{ route('departments.index') }}" class="menu-item" title="Departments">
                        <i class="fas fa-building"></i>
                        <span>Departments</span>
                    </a>
                    @endif
                    @if(auth()->user()->hasPermission('designations', 'view') || auth()->user()->isSuperAdmin())
                    <a href="{{ route('designations.index') }}" class="menu-item" title="Designations">
                        <i class="fas fa-user-tie"></i>
                        <span>Designations</span>
                    </a>
                    @endif
                    @if(auth()->user()->hasPermission('units', 'view'))
                    <a href="{{ route('units.index') }}" class="menu-item" title="Units">
                        <i class="fas fa-balance-scale"></i>
                        <span>Units</span>
                    </a>
                    @endif
                    @if(auth()->user()->hasPermission('raw-material-categories', 'view') || auth()->user()->isSuperAdmin())
                    <a href="{{ route('raw-material-categories.index') }}" class="menu-item" title="Raw Material Categories">
                        <i class="fas fa-layer-group"></i>
                        <span>Raw Material Categories</span>
                    </a>
                    @endif
                    @if(auth()->user()->hasPermission('raw-material-sub-categories', 'view') || auth()->user()->isSuperAdmin())
                    <a href="{{ route('raw-material-sub-categories.index') }}" class="menu-item" title="Raw Material SubCategories">
                        <i class="fas fa-sitemap"></i>
                        <span>Raw Material SubCategories</span>
                    </a>
                    @endif
                    @if(auth()->user()->hasPermission('raw-materials', 'view') || auth()->user()->isSuperAdmin())
                    <a href="{{ route('raw-materials.index') }}" class="menu-item" title="Raw Materials">
                        <i class="fas fa-cube"></i>
                        <span>Raw Materials</span>
                    </a>
                    @endif
                    @if(auth()->user()->hasPermission('product-categories', 'view') || auth()->user()->isSuperAdmin())
                    <a href="{{ route('product-categories.index') }}" class="menu-item" title="Product Categories">
                        <i class="fas fa-tags"></i>
                        <span>Product Categories</span>
                    </a>
                    @endif
                    @if(auth()->user()->hasPermission('products', 'view'))
                    <a href="{{ route('products.index') }}" class="menu-item" title="Products">
                        <i class="fas fa-box"></i>
                        <span>Products</span>
                    </a>
                    @endif
                    @if(auth()->user()->hasPermission('processes', 'view') || auth()->user()->isSuperAdmin())
                    <a href="{{ route('processes.index') }}" class="menu-item" title="Processes">
                        <i class="fas fa-cogs"></i>
                        <span>Processes</span>
                    </a>
                    @endif
                    @if(auth()->user()->hasPermission('bom-processes', 'view') || auth()->user()->isSuperAdmin())
                    <a href="{{ route('bom-processes.index') }}" class="menu-item" title="BOM Processes">
                        <i class="fas fa-clipboard-list"></i>
                        <span>BOM Processes</span>
                    </a>
                    @endif
                    @if(auth()->user()->hasPermission('employees', 'view') || auth()->user()->isSuperAdmin())
                    <a href="{{ route('employees.index') }}" class="menu-item" title="Employees">
                        <i class="fas fa-user-friends"></i>
                        <span>Employees</span>
                    </a>
                    @endif
                    @if(auth()->user()->hasPermission('customers', 'view'))
                    <a href="{{ route('customers.index') }}" class="menu-item" title="Customers">
                        <i class="fas fa-users"></i>
                        <span>Customers</span>
                    </a>
                    @endif
                    @if(auth()->user()->hasPermission('production-departments', 'view') || auth()->user()->isSuperAdmin())
                    <a href="{{ route('production-departments.index') }}" class="menu-item" title="Production Departments">
                        <i class="fas fa-industry"></i>
                        <span>Production Departments</span>
                    </a>
                    @endif
                    @if(auth()->user()->hasPermission('billing-addresses', 'view') || auth()->user()->isSuperAdmin())
                    <a href="{{ route('billing-addresses.index') }}" class="menu-item" title="Billing Addresses">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>Billing Addresses</span>
                    </a>
                    @endif
                </div>

                {{-- Settings Menu (Super Admin only) --}}
                @if(auth()->user()->isSuperAdmin())
                     <div class="menu-item-header" onclick="toggleSettingsMenu()" id="settingsHeader" style="margin-top: 10px;" title="Settings">
                         <i class="fas fa-cog menu-header-icon"></i>
                        <span>Settings</span>
                        <i class="fas fa-chevron-down arrow"></i>
                    </div>
                    <div class="menu-sub-items" id="settingsMenu">
                        <a href="{{ route('company-information.index') }}" class="menu-item" title="Company Information">
                            <i class="fas fa-building"></i>
                            <span>Company Information</span>
                        </a>
                    </div>
                @endif
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="main-content" id="mainContent">
            <!-- Top Header -->
            <header class="top-header">
                <div class="user-info" style="display: flex; align-items: center; gap: 15px;">
                    @php
                        $user = auth()->user();
                        $notificationCount = 0;
                        if ($user->isSuperAdmin() || $user->hasPermission('purchase-indents', 'approve')) {
                            $notificationCount = \App\Models\Notification::getUnreadCountForAdmins();
                        }
                    @endphp
                    
                    @if($user->isSuperAdmin() || $user->hasPermission('purchase-indents', 'approve'))
                        <a href="{{ route('notifications.index') }}" style="position: relative; padding: 8px 12px; background: rgba(255,255,255,0.2); border-radius: 5px; color: white; text-decoration: none; display: flex; align-items: center; gap: 8px;">
                            <i class="fas fa-bell"></i>
                            @if($notificationCount > 0)
                                <span style="position: absolute; top: -5px; right: -5px; background: #dc3545; color: white; border-radius: 50%; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: bold;">
                                    {{ $notificationCount > 99 ? '99+' : $notificationCount }}
                                </span>
                            @endif
                        </a>
                    @endif
                    
                    @if(auth()->user()->role)
                        <span class="role-badge">{{ auth()->user()->role->name }}</span>
                    @endif
                    
                    @php
                        $user = auth()->user();
                        $activeBranchId = session('active_branch_id');
                        $activeBranchName = session('active_branch_name');
                        // For Super Admin show all active branches; for others show only their active branches
                        $branchesForSelector = $user->isSuperAdmin()
                            ? \App\Models\Branch::where('is_active', true)->get()
                            : $user->branches()->where('is_active', true)->get();
                    @endphp

                    {{-- Branch Selector (top-right) --}}
                    @if($branchesForSelector->count() > 1)
                        <div style="position: relative;">
                            <select id="branch-selector" onchange="switchBranch(this.value)" 
                                style="padding: 8px 30px 8px 12px; border-radius: 5px; border: 1px solid rgba(255,255,255,0.3); background: rgba(255,255,255,0.2); color: white; font-size: 14px; cursor: pointer; appearance: none; background-image: url('data:image/svg+xml;utf8,<svg fill=\"white\" height=\"20\" viewBox=\"0 0 24 24\" width=\"20\" xmlns=\"http://www.w3.org/2000/svg\"><path d=\"M7 10l5 5 5-5z\"/></svg>'); background-repeat: no-repeat; background-position: right 8px center;">
                                @foreach($branchesForSelector as $branch)
                                    <option value="{{ $branch->id }}" {{ $activeBranchId == $branch->id ? 'selected' : '' }} style="background-color: #2c3e50; color: white;">
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @elseif($activeBranchName)
                        <span class="entity-badge" style="background: #f59e0b;">{{ $activeBranchName }}</span>
                    @endif
                    
                    <button class="logout-btn" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" title="Logout">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </button>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
            </header>
            
            <script>
                function switchBranch(branchId) {
                    if (branchId) {
                        window.location.href = '{{ url("/branches") }}/' + branchId + '/switch';
                    }
                }
            </script>

            <!-- Content Area -->
            <main class="content-area">
                @if(session('success'))
                    <div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 5px; margin-bottom: 20px;">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div style="background: #f8d7da; color: #721c24; padding: 12px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const toggleIcon = document.querySelector('.menu-toggle i');
            
            // Toggle collapsed state (show icons only)
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('sidebar-collapsed');
            
            // Update toggle icon based on state
            if (sidebar.classList.contains('collapsed')) {
                toggleIcon.classList.remove('fa-bars');
                toggleIcon.classList.add('fa-chevron-right');
            } else {
                toggleIcon.classList.remove('fa-chevron-right');
                toggleIcon.classList.add('fa-bars');
            }
            
            // Remove closed class if present (for mobile)
            sidebar.classList.remove('closed');
            mainContent.classList.remove('expanded');
        }
        
        // Handle mobile view (and restore sidebar when back to desktop)
        function handleMobileSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const isMobile = window.innerWidth <= 768;
            
            if (isMobile) {
                sidebar.classList.add('closed');
                sidebar.classList.remove('collapsed');
                mainContent.classList.add('expanded');
                mainContent.classList.remove('sidebar-collapsed');
            } else {
                // On desktop widths always show sidebar (unless user manually collapses it)
                sidebar.classList.remove('closed');
                mainContent.classList.remove('expanded');
            }
        }
        
        // Check on load and resize
        window.addEventListener('load', handleMobileSidebar);
        window.addEventListener('resize', handleMobileSidebar);

        // Toggle Masters menu
        function toggleMastersMenu() {
            const mastersMenu = document.getElementById('mastersMenu');
            const mastersHeader = document.getElementById('mastersHeader');
            
            if (mastersMenu && mastersHeader) {
                mastersMenu.classList.toggle('collapsed');
                mastersHeader.classList.toggle('collapsed');
                
                // Save state to localStorage
                localStorage.setItem('mastersMenuCollapsed', mastersMenu.classList.contains('collapsed'));
            }
        }

        // Toggle Tender Sales menu
        function toggleTenderSalesMenu() {
            const tenderSalesMenu = document.getElementById('tenderSalesMenu');
            const tenderSalesHeader = document.getElementById('tenderSalesHeader');
            
            if (tenderSalesMenu && tenderSalesHeader) {
                tenderSalesMenu.classList.toggle('collapsed');
                tenderSalesHeader.classList.toggle('collapsed');
                
                // Save state to localStorage
                localStorage.setItem('tenderSalesMenuCollapsed', tenderSalesMenu.classList.contains('collapsed'));
            }
        }

        // Toggle Enquiry Sales menu
        function toggleEnquirySalesMenu() {
            const enquirySalesMenu = document.getElementById('enquirySalesMenu');
            const enquirySalesHeader = document.getElementById('enquirySalesHeader');
            
            if (enquirySalesMenu && enquirySalesHeader) {
                enquirySalesMenu.classList.toggle('collapsed');
                enquirySalesHeader.classList.toggle('collapsed');
                
                // Save state to localStorage
                localStorage.setItem('enquirySalesMenuCollapsed', enquirySalesMenu.classList.contains('collapsed'));
            }
        }

        // Toggle Supplier menu
        function toggleSupplierMenu() {
            const supplierMenu = document.getElementById('supplierMenu');
            const supplierHeader = document.getElementById('supplierHeader');
            
            if (supplierMenu && supplierHeader) {
                supplierMenu.classList.toggle('collapsed');
                supplierHeader.classList.toggle('collapsed');
                
                // Save state to localStorage
                localStorage.setItem('supplierMenuCollapsed', supplierMenu.classList.contains('collapsed'));
            }
        }

        // Toggle Purchase menu
        function togglePurchaseMenu() {
            const purchaseMenu = document.getElementById('purchaseMenu');
            const purchaseHeader = document.getElementById('purchaseHeader');
            
            if (purchaseMenu && purchaseHeader) {
                purchaseMenu.classList.toggle('collapsed');
                purchaseHeader.classList.toggle('collapsed');
                
                // Save state to localStorage
                localStorage.setItem('purchaseMenuCollapsed', purchaseMenu.classList.contains('collapsed'));
            }
        }

        // Toggle Store menu
        function toggleStoreMenu() {
            const storeMenu = document.getElementById('storeMenu');
            const storeHeader = document.getElementById('storeHeader');
            
            if (storeMenu && storeHeader) {
                storeMenu.classList.toggle('collapsed');
                storeHeader.classList.toggle('collapsed');
                
                // Save state to localStorage
                localStorage.setItem('storeMenuCollapsed', storeMenu.classList.contains('collapsed'));
            }
        }

        // Toggle Settings menu
        function toggleSettingsMenu() {
            const settingsMenu = document.getElementById('settingsMenu');
            const settingsHeader = document.getElementById('settingsHeader');
            
            if (settingsMenu && settingsHeader) {
                settingsMenu.classList.toggle('collapsed');
                settingsHeader.classList.toggle('collapsed');
                
                // Save state to localStorage
                localStorage.setItem('settingsMenuCollapsed', settingsMenu.classList.contains('collapsed'));
            }
        }

        // Toggle System Admin menu
        function toggleSystemAdminMenu() {
            const systemAdminMenu = document.getElementById('systemAdminMenu');
            const systemAdminHeader = document.getElementById('systemAdminHeader');
            
            if (systemAdminMenu && systemAdminHeader) {
                systemAdminMenu.classList.toggle('collapsed');
                systemAdminHeader.classList.toggle('collapsed');
                
                // Save state to localStorage
                localStorage.setItem('systemAdminMenuCollapsed', systemAdminMenu.classList.contains('collapsed'));
            }
        }

        // Initialize all collapsible menus state on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Masters menu
            const mastersSavedState = localStorage.getItem('mastersMenuCollapsed');
            if (mastersSavedState === 'true') {
                const mastersMenu = document.getElementById('mastersMenu');
                const mastersHeader = document.getElementById('mastersHeader');
                if (mastersMenu && mastersHeader) {
                    mastersMenu.classList.add('collapsed');
                    mastersHeader.classList.add('collapsed');
                }
            }

            // Tender Sales menu
            const tenderSalesSavedState = localStorage.getItem('tenderSalesMenuCollapsed');
            if (tenderSalesSavedState === 'true') {
                const tenderSalesMenu = document.getElementById('tenderSalesMenu');
                const tenderSalesHeader = document.getElementById('tenderSalesHeader');
                if (tenderSalesMenu && tenderSalesHeader) {
                    tenderSalesMenu.classList.add('collapsed');
                    tenderSalesHeader.classList.add('collapsed');
                }
            }

            // Enquiry Sales menu
            const enquirySalesSavedState = localStorage.getItem('enquirySalesMenuCollapsed');
            if (enquirySalesSavedState === 'true') {
                const enquirySalesMenu = document.getElementById('enquirySalesMenu');
                const enquirySalesHeader = document.getElementById('enquirySalesHeader');
                if (enquirySalesMenu && enquirySalesHeader) {
                    enquirySalesMenu.classList.add('collapsed');
                    enquirySalesHeader.classList.add('collapsed');
                }
            }

            // Supplier menu
            const supplierSavedState = localStorage.getItem('supplierMenuCollapsed');
            if (supplierSavedState === 'true') {
                const supplierMenu = document.getElementById('supplierMenu');
                const supplierHeader = document.getElementById('supplierHeader');
                if (supplierMenu && supplierHeader) {
                    supplierMenu.classList.add('collapsed');
                    supplierHeader.classList.add('collapsed');
                }
            }

            // Purchase menu
            const purchaseSavedState = localStorage.getItem('purchaseMenuCollapsed');
            if (purchaseSavedState === 'true') {
                const purchaseMenu = document.getElementById('purchaseMenu');
                const purchaseHeader = document.getElementById('purchaseHeader');
                if (purchaseMenu && purchaseHeader) {
                    purchaseMenu.classList.add('collapsed');
                    purchaseHeader.classList.add('collapsed');
                }
            }

            // Store menu
            const storeSavedState = localStorage.getItem('storeMenuCollapsed');
            if (storeSavedState === 'true') {
                const storeMenu = document.getElementById('storeMenu');
                const storeHeader = document.getElementById('storeHeader');
                if (storeMenu && storeHeader) {
                    storeMenu.classList.add('collapsed');
                    storeHeader.classList.add('collapsed');
                }
            }

            // Settings menu
            const settingsSavedState = localStorage.getItem('settingsMenuCollapsed');
            if (settingsSavedState === 'true') {
                const settingsMenu = document.getElementById('settingsMenu');
                const settingsHeader = document.getElementById('settingsHeader');
                if (settingsMenu && settingsHeader) {
                    settingsMenu.classList.add('collapsed');
                    settingsHeader.classList.add('collapsed');
                }
            }

            // System Admin menu
            const systemAdminSavedState = localStorage.getItem('systemAdminMenuCollapsed');
            if (systemAdminSavedState === 'true') {
                const systemAdminMenu = document.getElementById('systemAdminMenu');
                const systemAdminHeader = document.getElementById('systemAdminHeader');
                if (systemAdminMenu && systemAdminHeader) {
                    systemAdminMenu.classList.add('collapsed');
                    systemAdminHeader.classList.add('collapsed');
                }
            }

            // Restore sidebar scroll position so it doesn't jump to top on navigation
            const sidebar = document.getElementById('sidebar');
            if (sidebar) {
                const savedScroll = localStorage.getItem('sidebarScrollTop');
                if (savedScroll !== null) {
                    sidebar.scrollTop = parseInt(savedScroll, 10) || 0;
                }
                
                // Persist scroll position while user scrolls
                sidebar.addEventListener('scroll', function () {
                    localStorage.setItem('sidebarScrollTop', sidebar.scrollTop);
                });
            }

            // Global form submit loader to prevent double submits and show progress
            document.querySelectorAll('form').forEach(function (form) {
                form.addEventListener('submit', function (e) {
                    // Prevent double submission
                    if (form.dataset.submitting === 'true') {
                        e.preventDefault();
                        return;
                    }
                    form.dataset.submitting = 'true';

                    const submitButtons = form.querySelectorAll('button[type="submit"], input[type="submit"]');
                    submitButtons.forEach(function (btn) {
                        // Skip if already processed
                        if (btn.dataset.loadingApplied === 'true') {
                            return;
                        }
                        btn.dataset.loadingApplied = 'true';
                        btn.disabled = true;

                        if (btn.tagName === 'BUTTON') {
                            btn.dataset.originalHtml = btn.innerHTML;
                            btn.innerHTML = '<span class="btn-loading-spinner"></span>Submitting...';
                        } else if (btn.tagName === 'INPUT') {
                            btn.dataset.originalValue = btn.value;
                            btn.value = 'Submitting...';
                        }
                    });
                });
            });
        });
    </script>
    @stack('scripts')
</body>
</html>


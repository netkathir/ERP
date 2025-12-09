<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_orders', function (Blueprint $table) {
            // Header / customer PO details
            $table->string('customer_po_no')->nullable();
            $table->date('customer_po_date')->nullable();
            $table->string('customer_tender_no')->nullable();
            $table->string('inspection_agency')->nullable();

            // Billing address
            $table->string('billing_address_line1')->nullable();
            $table->string('billing_address_line2')->nullable();
            $table->string('billing_city')->nullable();
            $table->string('billing_state')->nullable();
            $table->string('billing_pincode', 20)->nullable();
            $table->string('billing_gst_no', 50)->nullable();

            // Delivery address
            $table->string('delivery_address_line1')->nullable();
            $table->string('delivery_address_line2')->nullable();
            $table->string('delivery_city')->nullable();
            $table->string('delivery_state')->nullable();
            $table->string('delivery_pincode', 20)->nullable();
            $table->string('delivery_contact_no', 30)->nullable();

            // Tax / amount summary
            $table->enum('tax_type', ['cgst_sgst', 'igst'])->default('cgst_sgst');
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->decimal('gst_percent', 5, 2)->default(0);
            $table->decimal('cgst_percent', 5, 2)->default(0);
            $table->decimal('cgst_amount', 15, 2)->default(0);
            $table->decimal('sgst_percent', 5, 2)->default(0);
            $table->decimal('sgst_amount', 15, 2)->default(0);
            $table->decimal('igst_percent', 5, 2)->default(0);
            $table->decimal('igst_amount', 15, 2)->default(0);
            $table->decimal('freight', 15, 2)->default(0);
            $table->decimal('inspection_charges', 15, 2)->default(0);
            $table->decimal('net_amount', 15, 2)->default(0);
            $table->text('amount_note')->nullable();

            // Drawing / attachments / production
            $table->string('drawing_path')->nullable();
            $table->string('attachment_path')->nullable();
            $table->string('production_order_no')->nullable();
            $table->date('production_order_date')->nullable();
            $table->text('gf_mat_details')->nullable();
            $table->string('resin_type')->nullable();
            $table->text('packing_instructions')->nullable();

            // Prepared / approved
            $table->foreignId('prepared_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by_id')->nullable()->constrained('users')->nullOnDelete();
        });

        Schema::table('customer_order_items', function (Blueprint $table) {
            // Pricing / description for each line
            $table->text('description')->nullable();
            $table->string('pl_code')->nullable();
            $table->decimal('unit_price', 15, 4)->default(0);
            $table->decimal('installation_charges', 15, 2)->default(0);
            $table->decimal('line_amount', 15, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customer_order_items', function (Blueprint $table) {
            $table->dropColumn([
                'description',
                'pl_code',
                'unit_price',
                'installation_charges',
                'line_amount',
            ]);
        });

        Schema::table('customer_orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('prepared_by_id');
            $table->dropConstrainedForeignId('approved_by_id');

            $table->dropColumn([
                'customer_po_no',
                'customer_po_date',
                'customer_tender_no',
                'inspection_agency',
                'billing_address_line1',
                'billing_address_line2',
                'billing_city',
                'billing_state',
                'billing_pincode',
                'billing_gst_no',
                'delivery_address_line1',
                'delivery_address_line2',
                'delivery_city',
                'delivery_state',
                'delivery_pincode',
                'delivery_contact_no',
                'tax_type',
                'total_amount',
                'gst_percent',
                'cgst_percent',
                'cgst_amount',
                'sgst_percent',
                'sgst_amount',
                'igst_percent',
                'igst_amount',
                'freight',
                'inspection_charges',
                'net_amount',
                'amount_note',
                'drawing_path',
                'attachment_path',
                'production_order_no',
                'production_order_date',
                'gf_mat_details',
                'resin_type',
                'packing_instructions',
            ]);
        });
    }
};



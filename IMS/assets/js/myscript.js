/**
 * IMS Pro - Purchase Cart Logic
 */

let myCart = [];

/**
 * Add item to cart
 */
function addtocart() {
    const itemSelector = $("#item-selector");
    const itemName = itemSelector.val();
    const quantity = parseInt($("#quantity-input").val());
    const price = parseFloat(itemSelector.find("option:selected").attr("price"));
    const id = itemSelector.find("option:selected").attr("myid");

    if (!itemName || isNaN(quantity) || quantity <= 0) {
        alert("Please select an item and valid quantity.");
        return;
    }

    // Check if item already in cart
    const existing = myCart.find(item => item.id === id);
    if (existing) {
        existing.quantity += quantity;
    } else {
        myCart.push({ id, itemName, quantity, price });
    }

    displaycart();
}

/**
 * Render cart table
 */
function displaycart() {
    if (myCart.length === 0) {
        $("#tbl").html('<p class="text-muted" style="text-align: center; margin-top: 4rem;">No items added yet.</p>');
        $("#grand-total").text("NPR 0.00");
        $("#cart-count").text("0 Items");
        return;
    }

    let table = `<table class="display_table">
        <thead>
            <tr>
                <th>SN</th>
                <th>Item Name</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Total</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>`;
    
    let sumTotal = 0;
    myCart.forEach((item, i) => {
        const total = item.quantity * item.price;
        sumTotal += total;
        table += `<tr>
            <td>${i + 1}</td>
            <td style="font-weight: 600;">${item.itemName}</td>
            <td>${item.quantity}</td>
            <td>${item.price.toFixed(2)}</td>
            <td><strong>${total.toFixed(2)}</strong></td>
            <td>
                <button class="btn btn-danger" style="padding: 0.25rem 0.5rem;" onclick="deleteFromCart(${i})">
                    <i class="fas fa-times"></i>
                </button>
            </td>
        </tr>`;
    });

    table += `</tbody></table>`;
    
    $("#tbl").html(table);
    $("#grand-total").text(`NPR ${sumTotal.toLocaleString(undefined, {minimumFractionDigits: 2})}`);
    $("#cart-count").text(`${myCart.length} Items`);
}

/**
 * Remove item from cart
 */
function deleteFromCart(index) {
    myCart.splice(index, 1);
    displaycart();
}

/**
 * Save full purchase to DB
 */
function savePurchases() {
    const vendor = $("#vendor-input").val();
    
    if (!vendor) {
        alert("Please enter vendor name.");
        return;
    }

    if (myCart.length === 0) {
        alert("Cart is empty.");
        return;
    }

    // Step 1: Create Purchase Record
    $.ajax({
        url: "insertPurchase.php",
        method: "POST",
        data: { vendor: vendor },
        success: function(response) {
            // response should be the new purchase ID
            if ($.isNumeric(response)) {
                saveBill(response);
            } else {
                alert("Error creating purchase: " + response);
            }
        },
        error: function() {
            alert("Connection error.");
        }
    });
}

/**
 * Save each item to bill and update stock
 */
function saveBill(purchaseId) {
    let completed = 0;
    const totalItems = myCart.length;

    myCart.forEach(item => {
        $.ajax({
            url: "saveBill.php",
            type: "POST",
            data: {
                vendor_id: purchaseId,
                item_id: item.id,
                itemname: item.itemName,
                quantity: item.quantity,
                price: item.price
            },
            success: function(response) {
                completed++;
                if (completed === totalItems) {
                    showCompletion(purchaseId);
                }
            }
        });
    });
}

/**
 * Show success modal
 */
function showCompletion(purchaseId) {
    const vendor = $("#vendor-input").val();
    const total = $("#grand-total").text();
    
    let billHtml = `
        <div style="border-bottom: 1px dashed var(--border); padding-bottom: 1rem; margin-bottom: 1rem;">
            <p><strong>Vendor:</strong> ${vendor}</p>
            <p><strong>Date:</strong> ${new Date().toLocaleDateString()}</p>
            <p><strong>Ref #:</strong> PUR-${purchaseId}</p>
        </div>
        <table style="width: 100%; font-size: 0.9rem;">
            <thead><tr><th>Item</th><th style="text-align: right;">Total</th></tr></thead>
            <tbody>`;
            
    myCart.forEach(item => {
        billHtml += `<tr><td>${item.itemName} x ${item.quantity}</td><td style="text-align: right;">${(item.quantity * item.price).toFixed(2)}</td></tr>`;
    });
    
    billHtml += `</tbody>
        <tfoot>
            <tr><th style="text-align: left; padding-top: 1rem;">Total</th><th style="text-align: right; padding-top: 1rem;">${total}</th></tr>
        </tfoot>
    </table>`;

    $("#bill-summary").html(billHtml);
    $("#completion-modal").css("display", "flex");
}

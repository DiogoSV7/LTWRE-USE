async function fetchCartContents() {
    const response = await fetch('../api/api_cart_items.php');
    const cartContents = await response.json();

    updateCartDisplay(cartContents);
    updateButtonsAndForms(cartContents);
}

async function removeItemFromCart(event) {
    let itemId = event.target.getAttribute('data-id');
    const response = await fetch('../actions/action_remove_from_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `csrf=${temp}&idItem=${itemId}`
    });

    fetchCartContents();
}

function updateCartDisplay(cartItems) {
    let cartList = document.getElementById('cart-items');
    cartList.innerHTML = '';

    if (cartItems.length === 0) {
        cartList.innerHTML = '<li>Cart is empty</li>';
        return;
    }
    let totalPrice = 0;
    const categoryToEntityMap = {
        'Electronics': '&#128187;',
        'Clothing': '&#128084;',
        'Furniture': '&#129681;',
        'Books': '&#128218;',
        'Games': '&#127918;',
        'Sports': '&#9917;',
        'Homeware': '&#128250;',
        'Others': '&#128259;'
    };
    for (let i = 0; i < cartItems.length; i++) {
        let item = cartItems[i];
        let listItem = document.createElement('li');
       
        let itemLink = document.createElement('a');
        itemLink.href = '../pages/item.php?idItem=' + item.id;
        let emoji = categoryToEntityMap[item.category];
        if (emoji === undefined || emoji === "") {
            itemLink.innerHTML = `${item.name}`;
        } else {
            itemLink.innerHTML = `${emoji}: ${item.name}`;
        }
        listItem.appendChild(itemLink);

        listItem.innerHTML += ' - $' + item.price + ' ';

        let removeButton = document.createElement('button');
        removeButton.textContent = 'X';
        removeButton.className = 'remove-item-button';
        removeButton.setAttribute('data-id', item.id);
        removeButton.addEventListener('click', removeItemFromCart);

        listItem.appendChild(removeButton);

        cartList.appendChild(listItem);
        totalPrice += parseFloat(item.price);
    }

    let totalItem = document.createElement('p');
    totalItem.textContent = 'Total Price: $' + totalPrice.toFixed(2);
    cartList.appendChild(totalItem);
}

function updateButtonsAndForms(cartItems) {
    let addToCartForms = document.querySelectorAll('.add-to-cart');
    let removeFromCartForms = document.querySelectorAll('.remove-from-cart');

    let itemIds = cartItems.map(function(item) {
        return parseInt(item.id);
    });

    addToCartForms.forEach(function(form) {
        let itemIdInput = form.querySelector('input[name="idItem"]');
        if (!itemIdInput) 
            return;
        let itemId = parseInt(itemIdInput.value);

        if (itemIds.includes(itemId)) {
            form.setAttribute('action', '../actions/action_remove_from_cart.php');
            form.classList.remove('add-to-cart');
            form.classList.add('remove-from-cart');
            let button = form.querySelector('button');
            button.textContent = 'Remove from Cart';
            form.removeEventListener('submit', addToCart);
            form.addEventListener('submit', removeFromCart);
        } else {
            form.setAttribute('action', '../actions/action_add_to_cart.php');
            form.classList.remove('remove-from-cart');
            form.classList.add('add-to-cart');
            let button = form.querySelector('button');
            button.textContent = 'Add to Cart';
            form.removeEventListener('submit', removeFromCart);
            form.addEventListener('submit', addToCart);
        }
    });

    removeFromCartForms.forEach(function(form) {
        let itemIdInput = form.querySelector('input[name="idItem"]');
        if (!itemIdInput) 
            return;
        let itemId = parseInt(itemIdInput.value);

        if (itemIds.includes(itemId)) {
            form.setAttribute('action', '../actions/action_remove_from_cart.php');
            form.classList.remove('add-to-cart');
            form.classList.add('remove-from-cart');
            let button = form.querySelector('button');
            button.textContent = 'Remove from Cart';
            form.removeEventListener('submit', addToCart);
            form.addEventListener('submit', removeFromCart);
        } else {
            form.setAttribute('action', '../actions/action_add_to_cart.php');
            form.classList.remove('remove-from-cart');
            form.classList.add('add-to-cart');
            let button = form.querySelector('button');
            button.textContent = 'Add to Cart';
            form.removeEventListener('submit', removeFromCart);
            form.addEventListener('submit', addToCart);
        }
    });

    let checkoutButton = document.getElementById('checkout-button');
    if (checkoutButton) {
        checkoutButton.style.display = cartItems.length > 0 ? 'block' : 'none';
    }
}


async function addToCart(event) {
    event.preventDefault();
    let itemId = this.querySelector('input[name="idItem"]').value;

    const response = await fetch('../actions/action_add_to_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `csrf=${temp}&idItem=${itemId}`
    });
    
    fetchCartContents();
}

async function removeFromCart(event) {
    event.preventDefault();
    let itemId = this.querySelector('input[name="idItem"]').value;

    const response = await fetch('../actions/action_remove_from_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `csrf=${temp}&idItem=${itemId}`
    });

    fetchCartContents();
}

document.addEventListener('DOMContentLoaded', function() {
    fetchCartContents();

    document.querySelectorAll('.add-to-cart').forEach(function(form) {
        form.addEventListener('submit', addToCart);
    });

    document.querySelectorAll('.remove-from-cart').forEach(function(form) {
        form.addEventListener('submit', removeFromCart);
    });
});



document.addEventListener('DOMContentLoaded', function() {
    let cartIcon = document.getElementById('cart-icon');
    let cartItemsContainer = document.getElementById('cart-items');
    let checkoutForm = document.getElementById('checkout-form');

    if (cartIcon){
        cartIcon.addEventListener('click', function(event) {
        event.preventDefault();
        if (cartItemsContainer.style.display === 'none') {
            cartItemsContainer.style.display = 'block';
            checkoutForm.style.display = 'block';
        } else {
            cartItemsContainer.style.display = 'none';
            checkoutForm.style.display = 'none';
        }
        });
    }
});

document.addEventListener('DOMContentLoaded', function() {
    let cartIcon = document.getElementById('cart-icon');
    let cartContainer = document.getElementById('cart-container');

    if (cartIcon){
        cartIcon.addEventListener('click', function(event) {
            event.preventDefault();
            if (cartContainer.style.display === 'none') {
                cartContainer.style.display = 'block';
            } else {
                cartContainer.style.display = 'none';
            }
        });
    }
});

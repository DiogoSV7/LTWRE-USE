let time = 0;

function searchItemsWithDelay(formData) {
    clearTimeout(time);
    time = setTimeout(function() {
        searchItems(formData);
    }, 300); //300 ms
}

async function searchItems(formData) {
    let query = new URLSearchParams(formData).toString();

    const response = await fetch('../api/api_search.php?' + query);
    const data = await response.json();

    let itemsSection = document.getElementById('items');
    itemsSection.innerHTML = '';

    let itemsHeader = document.createElement('h2');
    itemsHeader.textContent = 'Items for Sale';
    itemsSection.appendChild(itemsHeader);

    data.forEach(function(item) {
        let itemElement = document.createElement('article');
        let itemDetails = `
                <a href="../pages/item.php?idItem=${item.id}">
                    <img src="${item.image}" alt="${item.name}">
                </a>
                <div class="item-details">
                    <h2>
                        <a href="../pages/item.php?idItem=${item.id}">${item.name}</a>
                    </h2>
                    <h3>${item.brand} - ${item.model}</h3>
                    <p>Price: ${item.price}€</p>`;
        
        if (isLogged) {
            let formElement = document.createElement('form');
            formElement.setAttribute('action', '../actions/action_add_to_cart.php');
            formElement.setAttribute('method', 'post');

            let csrfInput = document.createElement('input');
            csrfInput.setAttribute('type', 'hidden');
            csrfInput.setAttribute('name', 'csrf');
            csrfInput.setAttribute('value', temp);
            formElement.appendChild(csrfInput);

            let idItemInput = document.createElement('input');
            idItemInput.setAttribute('type', 'hidden');
            idItemInput.setAttribute('name', 'idItem');
            idItemInput.setAttribute('value', item.id);
            formElement.appendChild(idItemInput);

            let addToCartButton = document.createElement('button');
            addToCartButton.setAttribute('type', 'submit');
            addToCartButton.textContent = 'Add to Cart';

            formElement.appendChild(addToCartButton);

            itemDetails += formElement.outerHTML;
        }

        itemDetails += `</div>`;
        itemElement.innerHTML = itemDetails;
        itemsSection.appendChild(itemElement);
    });

}

document.getElementById('search-form').addEventListener('input', function(event) {
    event.preventDefault();
    let formData = new FormData(document.getElementById('search-form'));
    searchItemsWithDelay(formData);
});

document.getElementById('search-button').addEventListener('click', function(event) {
    event.preventDefault();
    let formData = new FormData(document.getElementById('search-form'));
    searchItems(formData);
});

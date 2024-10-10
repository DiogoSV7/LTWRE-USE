document.addEventListener("DOMContentLoaded", function() {
    async function loadContent(url, containerId) {
        const response = await fetch(url);
        const data = await response.text();
        document.getElementById(containerId).innerHTML = data;

        setupUserSearchListener();
    }

    document.getElementById('user-details').addEventListener('click', function(e) {
        e.preventDefault(); 
        loadContent('profile_user_details.php?section=container', 'content-container'); 
    });

    document.getElementById('wishlist').addEventListener('click', function(e) {
        e.preventDefault();
        loadContent('wishlist.php?section=container', 'content-container');
    });

    document.getElementById('your-items').addEventListener('click', function(e) {
        e.preventDefault();
        loadContent('profile_your_items.php?section=container', 'content-container');
    });

    document.getElementById('your-orders').addEventListener('click', function(e) {
        e.preventDefault();
        loadContent('profile_your_orders.php?section=container', 'content-container');
    });

    document.getElementById('orders-to-ship').addEventListener('click', function(e) {
        e.preventDefault();
        loadContent('profile_orders_to_ship.php?section=container', 'content-container');
    });

    if (isAdmin) {
        document.getElementById('admin-page').addEventListener('click', function(e) {
            e.preventDefault(); 
            loadContent('admin-page.php?section=container', 'content-container');
        });
    }

    function setupUserSearchListener() {
        const userSearchInput = document.getElementById('user_search');
        if (userSearchInput) {
            userSearchInput.addEventListener('input', function() {
                const search = this.value;
        
                fetch(`../api/api_search_user.php?search=${encodeURIComponent(search)}`)
                    .then(response => response.json())
                    .then(users => {
                        const userList = document.getElementById('user_list');
                        userList.innerHTML = '';
        
                        users.forEach(user => {
                            const userDisplay = `${user.username} (${user.name})`;
                            const adminTag = user.isAdmin ? ' - <strong>Admin</strong>' : '';
                            const userDiv = document.createElement('div');
        
                            userDiv.classList.add('user-item');
        
                            const radioInput = document.createElement('input');
                            radioInput.type = 'radio';
                            radioInput.id = `user_${user.idUser}`;
                            radioInput.name = 'user_id';
                            radioInput.value = user.idUser;
        
                            const label = document.createElement('label');
                            label.htmlFor = `user_${user.idUser}`;
                            label.innerHTML = userDisplay + adminTag;
        
                            userDiv.appendChild(radioInput);
                            userDiv.appendChild(label);
                            userList.appendChild(userDiv);
                        });
                    })
                    .catch(error => console.error('Error:', error));
            });
        }
    }
});

function checkEnterPress(event) {
    if (event.keyCode === 13) {
        event.preventDefault();
        return false;
    }
}

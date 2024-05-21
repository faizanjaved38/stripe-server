var hostname;

document.addEventListener('DOMContentLoaded', async () => {
  let searchParams = new URLSearchParams(window.location.search);
  if (searchParams.has('session_id')) {
    const session_id = searchParams.get('session_id');
    document.getElementById('session-id').setAttribute('value', session_id);
  }
});

document.addEventListener('DOMContentLoaded', () => {
  const params = new URLSearchParams(window.location.search);
  hostname = params.get('hostname');

  if (hostname) {
    checkHostname(hostname);
  }
});

function displaySubscriptions(subscriptions) {
  let container = document.getElementById('subscriptions-container');
  container.innerHTML = ''; // Clear previous content

  subscriptions.forEach(subscription => {
    let subscriptionDiv = document.createElement('div');
    subscriptionDiv.innerHTML = `
          <h3><strong>ID:</strong> ${subscription.id}</h3>
          <h5><strong>Status:</strong> ${subscription.status}</h5>
          <button id="checkout-and-portal-button" onclick="cancelSubscription('${subscription.id}')">Cancel Subscription</button>
      `;
    container.appendChild(subscriptionDiv);
  });
}

function fetchSubscriptions(customerId) {
  fetch(`http://localhost:4242/get-subscription.php?customer_id=${customerId}`)
    .then(response => response.json())
    .then(data => {
      if (data) {
        displaySubscriptions(data.data);
      } else {
        displayMessage('No subscriptions found.');
      }
    })
    .catch(error => {
      console.error('Error fetching subscriptions:', error);
    });
}

function checkHostname(hostname) {
  fetch(`http://localhost:4242/user.php?hostname=${hostname}`)
    .then(response => response.json())
    .then(data => {
      if (data.exists) {
        fetchSubscriptions(data.customer_id);
      } else {
        displayMessage(`<div class="description">
        <h3>Starter plan</h3>
        <h5>$33.00 / month</h5>
      </div>
    </div>
    <form action="/create-checkout-session.php" method="POST">
      <input type="hidden" name="lookup_key" value="1234" />
      <input type="hidden" name="hostname" id="hostname" value="${hostname}" />
      <button id="checkout-and-portal-button" type="submit">Checkout</button>
    </form>`);
      }
    })
    .catch(error => {
      displayMessage(error.message);
    });
}

function displayMessage(message) {
  let container = document.getElementById('message-container');
  container.innerHTML = message;
}

function cancelSubscription(subscriptionId) {
  fetch('http://localhost:4242/cancel-subscription.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({ subscription_id: subscriptionId })
  })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        displayMessage('<h3>Subscription cancelled successfully.</h3>');
        let container = document.getElementById('subscriptions-container');
        container.innerHTML = '';
        setTimeout(() => {
          window.location.href = `http://${hostname}:8443/admin`;
        }, 1000);
      } else {
        displayMessage('Failed to cancel subscription: ' + data.error);
      }
    })
    .catch(error => {
      console.error('Error cancelling subscription:', error);
    });
}

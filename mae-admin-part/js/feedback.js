// Function to load feedback data
function loadFeedback() {
    console.log('Loading feedback data...');
    fetch('process_feedback.php')
        .then(response => {
            console.log('Response received:', response);
            return response.json();
        })
        .then(data => {
            console.log('Data received:', data);
            if (data.success) {
                const tbody = document.querySelector('#feedbackTable tbody');
                tbody.innerHTML = '';
                
                if (data.data.length === 0) {
                    const row = document.createElement('tr');
                    row.innerHTML = '<td colspan="6" class="text-center">No feedback found</td>';
                    tbody.appendChild(row);
                    return;
                }
                
                data.data.forEach(feedback => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${feedback.requester_name}</td>
                        <td>${feedback.service_name}</td>
                        <td>${generateStars(feedback.rating)}</td>
                        <td>${feedback.feedback_text}</td>
                        <td>${new Date(feedback.created_at).toLocaleDateString()}</td>
                        <td>
                            <button class="btn btn-sm btn-info" onclick="viewFeedback(${feedback.id})">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="deleteFeedback(${feedback.id})">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    `;
                    tbody.appendChild(row);
                });
            } else {
                console.error('Error loading feedback:', data.message);
                alert('Error loading feedback: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error loading feedback:', error);
            alert('Error loading feedback. Please check the console for details.');
        });
}

// Function to generate star rating display
function generateStars(rating) {
    let stars = '';
    for (let i = 1; i <= 5; i++) {
        stars += `<i class="bi bi-star${i <= rating ? '-fill' : ''} text-warning"></i>`;
    }
    return stars;
}

// Function to view feedback details
function viewFeedback(id) {
    console.log('Viewing feedback:', id);
    fetch(`process_feedback.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const feedback = data.data[0];
                document.getElementById('feedbackMember').textContent = feedback.requester_name;
                document.getElementById('feedbackEvent').textContent = feedback.service_name;
                document.getElementById('feedbackRating').innerHTML = generateStars(feedback.rating);
                document.getElementById('feedbackContent').textContent = feedback.feedback_text;
                document.getElementById('feedbackDate').textContent = new Date(feedback.created_at).toLocaleDateString();
                
                const modal = new bootstrap.Modal(document.getElementById('feedbackModal'));
                modal.show();
            }
        })
        .catch(error => console.error('Error loading feedback details:', error));
}

// Function to delete feedback
function deleteFeedback(id) {
    if (confirm('Are you sure you want to delete this feedback?')) {
        fetch('process_feedback.php', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id: id })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadFeedback();
                const modal = bootstrap.Modal.getInstance(document.getElementById('feedbackModal'));
                if (modal) modal.hide();
            }
        })
        .catch(error => console.error('Error deleting feedback:', error));
    }
}

// Function to apply filters
function filterFeedback() {
    const eventFilter = document.getElementById('eventFilter').value;
    const dateFilter = document.getElementById('dateFilter').value;
    
    console.log('Applying filters:', { eventFilter, dateFilter });
    
    fetch(`process_feedback.php?event=${eventFilter}&date=${dateFilter}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadFeedback();
            }
        })
        .catch(error => console.error('Error filtering feedback:', error));
}

// Load feedback when page loads
document.addEventListener('DOMContentLoaded', function() {
    console.log('Page loaded, initializing feedback...');
    loadFeedback();
}); 
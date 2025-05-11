// Gallery Management JavaScript

// Initialize the gallery when the page loads
document.addEventListener('DOMContentLoaded', function() {
    loadGallery();
});

// Function to load gallery images
function loadGallery() {
    fetch('api/gallery.php?action=get')
        .then(response => response.json())
        .then(data => {
            const galleryGrid = document.getElementById('galleryGrid');
            galleryGrid.innerHTML = '';
            
            data.forEach(image => {
                const imageCard = createImageCard(image);
                galleryGrid.appendChild(imageCard);
            });
        })
        .catch(error => console.error('Error loading gallery:', error));
}

// Function to create an image card
function createImageCard(image) {
    const col = document.createElement('div');
    col.className = 'col-md-4 col-lg-3';
    
    col.innerHTML = `
        <div class="card h-100">
            <img src="${image.path}" class="card-img-top" alt="${image.title}" style="height: 200px; object-fit: cover;">
            <div class="card-body">
                <h5 class="card-title">${image.title}</h5>
                <p class="card-text small text-muted">${image.event}</p>
                <button class="btn btn-sm btn-primary" onclick="previewImage(${image.id})">
                    <i class="bi bi-eye"></i> View
                </button>
            </div>
        </div>
    `;
    
    return col;
}

// Function to handle image upload
function uploadImages() {
    const formData = new FormData();
    const eventName = document.getElementById('uploadEvent').value;
    const title = document.getElementById('imageTitle').value;
    const description = document.getElementById('imageDescription').value;
    const files = document.getElementById('imageFiles').files;

    if (!eventName || !title || files.length === 0) {
        alert('Please fill in all required fields and select at least one image.');
        return;
    }

    formData.append('event', eventName);
    formData.append('title', title);
    formData.append('description', description);
    
    for (let i = 0; i < files.length; i++) {
        formData.append('images[]', files[i]);
    }

    // Show loading state
    const uploadButton = document.querySelector('#uploadModal .btn-primary');
    const originalText = uploadButton.innerHTML;
    uploadButton.innerHTML = '<i class="bi bi-arrow-repeat spin"></i> Uploading...';
    uploadButton.disabled = true;

    fetch('api/gallery.php?action=upload', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Images uploaded successfully!');
            // Close modal and reset form
            const modal = bootstrap.Modal.getInstance(document.getElementById('uploadModal'));
            modal.hide();
            document.getElementById('uploadForm').reset();
            // Reload gallery
            loadGallery();
        } else {
            alert('Error uploading images: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error uploading images. Please try again.');
    })
    .finally(() => {
        // Reset button state
        uploadButton.innerHTML = originalText;
        uploadButton.disabled = false;
    });
}

// Function to preview image
function previewImage(imageId) {
    fetch(`api/gallery.php?action=get&id=${imageId}`)
        .then(response => response.json())
        .then(image => {
            document.getElementById('previewImage').src = image.path;
            document.getElementById('previewTitle').textContent = image.title;
            document.getElementById('previewDescription').textContent = image.description;
            document.getElementById('previewEvent').textContent = image.event;
            document.getElementById('previewDate').textContent = new Date(image.upload_date).toLocaleDateString();
            
            // Store the current image ID for deletion
            document.getElementById('imagePreviewModal').dataset.imageId = imageId;
            
            // Show the modal
            const modal = new bootstrap.Modal(document.getElementById('imagePreviewModal'));
            modal.show();
        })
        .catch(error => console.error('Error loading image details:', error));
}

// Function to delete image
function deleteImage() {
    const imageId = document.getElementById('imagePreviewModal').dataset.imageId;
    
    if (confirm('Are you sure you want to delete this image?')) {
        fetch(`api/gallery.php?action=delete&id=${imageId}`, {
            method: 'DELETE'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Close modal and reload gallery
                const modal = bootstrap.Modal.getInstance(document.getElementById('imagePreviewModal'));
                modal.hide();
                loadGallery();
            } else {
                alert('Error deleting image: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting image. Please try again.');
        });
    }
}

// Function to filter gallery
function filterGallery() {
    const eventFilter = document.getElementById('eventFilter').value;
    const dateFilter = document.getElementById('dateFilter').value;
    
    fetch(`api/gallery.php?action=get&event=${eventFilter}&date=${dateFilter}`)
        .then(response => response.json())
        .then(data => {
            const galleryGrid = document.getElementById('galleryGrid');
            galleryGrid.innerHTML = '';
            
            data.forEach(image => {
                const imageCard = createImageCard(image);
                galleryGrid.appendChild(imageCard);
            });
        })
        .catch(error => console.error('Error filtering gallery:', error));
} 
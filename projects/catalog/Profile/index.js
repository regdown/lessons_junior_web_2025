document.addEventListener('DOMContentLoaded', function() {
    const uploadInput = document.getElementById("upload");
    const avatarImage = document.getElementById("avatar");
    const avatarContainer = document.getElementById("avatarContainer");

    avatarContainer.addEventListener("click", function() {
        uploadInput.click();
    });

    uploadInput.addEventListener("change", function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                avatarImage.src = e.target.result;
            }
            reader.readAsDataURL(file);
        }
    });
});
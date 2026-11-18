function handleImageHandler(){


	<!-- The Modal -->
	full_modal_html = `
        <div id="myFullImageModal" class="modal">
        <span class="close">&times;</span>
        <img class="modal-content" id="myFullimg01">
        <div id="myFullcaption"></div>
        </div>`;

	if($('body').find('#myFullImageModal').length === 0){
		$('body').append(full_modal_html);
	}

	// Get the modal
	myFullImgModal = document.getElementById("myFullImageModal");

	// Get the image and insert it inside the modal - use its "alt" text as a caption
	googleLocationImg = document.querySelector(".google-location-img");
	myFullModalImg = document.getElementById("myFullimg01");
	captionText = document.getElementById("myFullcaption");
	googleLocationImg.onclick = function(){
		myFullImgModal.style.display = "block";
		myFullModalImg.src = this.src;
		captionText.innerHTML = this.alt;
		$("#widgetVoxeet").hide();
	};

	// Get the <span> element that closes the modal
	myFullModalSpan = document.querySelector("#myFullImageModal .close");

	// When the user clicks on <span> (x), close the modal
	myFullModalSpan.onclick = function() {
		myFullImgModal.style.display = "none";
		$("#widgetVoxeet").show();
	};


}
import $ from 'jquery';

class Search {
	// Describe and create/initiate our object
	constructor() {
		this.openButton = $(".js-search-trigger");
		this.closeButton = $(".search-overlay__close");
		this.searchOverlay = $(".search-overlay");
		this.searchField = $("#search-term");
		this.resultsDiv = $("#search-overlay__results");

		this.isOverlayOpen = false;
		this.isSpinnerVisible = false;
		this.searchTimer;
		this.previousValue;

		this.events();
	}

	// Events
	events() {
		this.openButton.on('click', this.openOverlay.bind(this));
		this.closeButton.on('click', this.closeOverlay.bind(this));
		$(document).on('keydown', this.keyPressDispatcher.bind(this));
		this.searchField.on('keyup', this.typingLogic.bind(this));
	}

	// Methods
	typingLogic() {
		if (this.previousValue != this.searchField.val()) {
			clearTimeout(this.searchTimer);

			if (this.searchField.val()) {
				if (!this.isSpinnerVisible) {
					this.resultsDiv.html(`<div class="spinner-loader"></div>`);
					this.isSpinnerVisible = true;
				}
				this.searchTimer = setTimeout(this.getResults.bind(this), 800);
			} else {
				this.resultsDiv.html("");
				this.isSpinnerVisible = false;
			}
		}
		this.previousValue = this.searchField.val();
	}

	getResults() {
		this.resultsDiv.html("This text will be replaced with real data");
		this.isSpinnerVisible = false;
	}

	keyPressDispatcher(e) {
		if (e.keyCode == 83 && !this.isOverlayOpen && !$("input, textarea").is(':focus')) {
			this.openOverlay();
		}

		if (e.keyCode == 27 && this.isOverlayOpen) {
			this.closeOverlay();
		}
	}

	openOverlay() {
		this.searchOverlay.addClass("search-overlay--active");
		$("body").addClass("body-no-scroll");
		this.isOverlayOpen = true;
	}

	closeOverlay() {
		this.searchOverlay.removeClass("search-overlay--active");
		$("body").removeClass("body-no-scroll");
		this.isOverlayOpen = false;
	}
}

export default Search;
import $ from "jquery";

class Like {
  constructor() {
    this.events();
  }

  events() {
    $(".like-box").on("click", this.clickDispatcher.bind(this));
  }

  // Methods
  clickDispatcher(e) {
    const currentLikeBox = $(e.target).closest(".like-box");

    if (currentLikeBox.attr("data-exists") == "yes") {
      this.deleteLike(currentLikeBox);
    } else {
      this.createLike(currentLikeBox);
    }
  }

  createLike(currentLikeBox) {
    $.ajax({
      beforeSend: xhr => {
        xhr.setRequestHeader("X-WP-Nonce", universityData.nonce);
      },
      url: universityData.root_url + "/wp-json/uni/v1/manageLike",
      type: "POST",
      data: { professorID: currentLikeBox.data("professor") },
      success: response => {
        currentLikeBox.attr("data-exists", "yes");
        let likeCount = Number(currentLikeBox.find(".like-count").html());
        likeCount++;
        currentLikeBox.find(".like-count").html(likeCount);
        currentLikeBox.attr("data-like", response);

        console.log(response);
      },
      error: err => {
        console.log(err);
      }
    });
  }

  deleteLike(currentLikeBox) {
    $.ajax({
      beforeSend: xhr => {
        xhr.setRequestHeader("X-WP-Nonce", universityData.nonce);
      },
      url: universityData.root_url + "/wp-json/uni/v1/manageLike",
      type: "DELETE",
      data: { like: currentLikeBox.attr("data-like") },
      success: response => {
        currentLikeBox.attr("data-exists", "no");

        let likeCount = Number(currentLikeBox.find(".like-count").html());
        likeCount--;
        currentLikeBox.find(".like-count").html(likeCount);
        currentLikeBox.attr("data-like", "");

        console.log(response);
      },
      error: err => {
        console.log(err);
      }
    });
  }
}

export default Like;

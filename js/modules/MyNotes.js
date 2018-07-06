import $ from "jquery";

class MyNotes {
  constructor() {
    this.events();
  }

  events() {
    $("#my-notes").on("click", ".delete-note", this.deleteNote);
    $("#my-notes").on("click", ".edit-note", this.editNote.bind(this));
    $("#my-notes").on("click", ".update-note", this.updateNote.bind(this));
    $(".submit-note").on("click", this.createNote);
  }

  // Methods
  editNote(e) {
    const note = $(e.target).parents("li");
    const noteID = note.data("id");

    if (note.data("state") == "editable") {
      this.makeNoteReadOnly(note);
    } else {
      this.makeNoteEditable(note);
    }
  }

  makeNoteEditable(note) {
    note
      .find(".edit-note")
      .html(`<i class="fa fa-times" aria-hidden="true"></i> Cancel`);
    note
      .find(".note-title-field, .note-body-field")
      .removeAttr("readonly")
      .addClass("note-active-field");
    note.find(".update-note").addClass("update-note--visible");
    note.data("state", "editable");
  }

  makeNoteReadOnly(note) {
    note
      .find(".edit-note")
      .html(`<i class="fa fa-pencil" aria-hidden="true"></i> Edit`);
    note
      .find(".note-title-field, .note-body-field")
      .attr("readonly", "readonly")
      .removeClass("note-active-field");
    note.find(".update-note").removeClass("update-note--visible");
    note.data("state", "cancel");
  }

  updateNote(e) {
    const note = $(e.target).parents("li");
    const noteID = note.data("id");
    const updatedPost = {
      title: note.find(".note-title-field").val(),
      content: note.find(".note-body-field").val()
    };

    $.ajax({
      beforeSend: xhr => {
        xhr.setRequestHeader("X-WP-Nonce", universityData.nonce);
      },
      url: universityData.root_url + "/wp-json/wp/v2/note/" + noteID,
      type: "POST",
      data: updatedPost,
      success: data => {
        console.log(data);
        this.makeNoteReadOnly(note);
      },
      error: err => {
        console.log(err);
      }
    });
  }

  createNote(e) {
    const newPost = {
      title: $(".new-note-title").val(),
      content: $(".new-note-body").val(),
      status: "publish"
    };

    $.ajax({
      beforeSend: xhr => {
        xhr.setRequestHeader("X-WP-Nonce", universityData.nonce);
      },
      url: universityData.root_url + "/wp-json/wp/v2/note/",
      type: "POST",
      data: newPost,
      success: response => {
        console.log(response);

        $(".new-note-title, .new-note-body").val("");

        $(`
          <li data-id="${response.id}">
            <input readonly type="text" class="note-title-field" value="${
              response.title.raw
            }">

            <span class="edit-note"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</span>
            <span class="delete-note"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete</span>
            
            <textarea readonly class="note-body-field" name="" id="">${
              response.content.raw
            }</textarea>

            <span class="update-note btn btn--blue btn--small"><i class="fa fa-arrow-right" aria-hidden="true"></i> Save</span>
          </li>
        `)
          .prependTo("#my-notes")
          .hide()
          .slideDown();
      },
      error: err => {
        console.log(err);
      }
    });
  }

  deleteNote(e) {
    const note = $(e.target).parents("li");
    const noteID = note.data("id");

    $.ajax({
      beforeSend: xhr => {
        xhr.setRequestHeader("X-WP-Nonce", universityData.nonce);
      },
      url: universityData.root_url + "/wp-json/wp/v2/note/" + noteID,
      type: "DELETE",
      success: data => {
        note.slideUp();
      },
      error: err => {
        console.log(err);
      }
    });
  }
}

export default MyNotes;

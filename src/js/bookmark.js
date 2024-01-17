new Vue({
    el: '#bookmark-app',
    data() {
      return {
        userId: '',
        tags: [],
        tagname: '',
        tagscount: 0,
        selectedTag: '',
        message: '',
        bookmarks: [],
        bookmarkscount: 0,
        showModal: false,
        editedComment: '', 
        editingBookmark:'' 
    };
    },
    created() {
      this.fetchData(); 
    },
    methods: {
      fetchData() {
        this.userId = new URLSearchParams(window.location.search).get('user_id');
  
        if (this.userId) {
          axios.get(`php/getbookmarkDB.php?user_id=${this.userId}`)
            .then(response => {
              const userbookmarks = response.data.bookmarks;
              const bookmarkscount = response.data.bookmarkcnt;
              this.bookmarks = userbookmarks;
              this.bookmarkscount = bookmarkscount;
            })
            .catch(error => {
              console.error('Error fetching user bookmarks:', error);
            });
        }
  
        if (this.userId) {
          axios.get(`php/gettagsDB.php?user_id=${this.userId}`)
            .then(response => {
              this.tags = response.data.gettags;
              if (this.tags) {
                this.tagscount = response.data.tagscount;
              } else {
                this.tagscount = 0;
              }
            })
            .catch(error => {
              console.error('Error fetching tags:', error);
            });
        }
      },
      filterBookmarks() {
        if (this.selectedTag !== '') {
          axios.get(`php/getbookmarkDB.php?user_id=${this.userId}&tag_id=${this.selectedTag}`)
            .then(response => {
              const userbookmarks = response.data.bookmarks;
              this.bookmarks = userbookmarks;
            })
            .catch(error => {
              console.error('Error fetching user bookmarks:', error);
            });
        } else {
          this.fetchData();
        }
      },
      getTagName(tagId) {
        const tag = this.tags.find(tag => tag.tag_id === tagId);
        return tag ? tag.tag_name : '未分類';
      },
      commentupdate(bookmark) {
        this.editedComment = bookmark.comment;
        this.editingBookmark = bookmark;
        this.showModal = true;
      },

      saveComment() {
        if (this.editingBookmark) {
          this.editingBookmark.comment = this.editedComment;
          axios.post('php/updateComment.php', {
            bookmark_id: this.editingBookmark.bookmark_id,
            comment: this.editedComment
          })
          .then(response => {
            alert(`${this.editingBookmark.title}のコメントを更新しました`);
          })
          .catch(error => {
            console.error('Error updating comment:', error);
          })
          .finally(() => {
            this.closeModal();
          });
        }
      },

      closeModal() {
        this.showModal = false;
        this.editingBookmark = '';
        this.editedComment = '';
      },
      bookmarkdelete(bookmark_id) {
        const isConfirmed = window.confirm('本当に削除しますか？');
        if (isConfirmed) {
          axios.post('php/deleteBookmark.php', {
            bookmark_id: bookmark_id
          })
          .then(response => {
            alert('ブックマークを削除しました');
            this.fetchData();
          })
          .catch(error => {
            console.error('Error deleting bookmark:', error);
          })
        }
      },
      goBack() {
        window.history.back();
      },
    }
});
  
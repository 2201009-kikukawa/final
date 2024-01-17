const app1 = new Vue({
    el: '#app',
    data() {
        return {
            keyword: '',
            message: '',
            items: [],
            selectedItem: null,
            comment: '',
            userProfile: null,
            tags: [], 
            selectedTag: null, 
            perPage: 20,
            currentPage: 1,
            totalPages: 1
        };
    },
    watch: {
        keyword: function (newKW, oldKW) {
            this.message = 'Waiting 6 seconds for your typing...'
            this.debouncedGetAnswer()
        }

    },
    created: function () {
        this.debouncedGetAnswer = _.debounce(this.getAnswer, 6000);
    },
    mounted : function(){
        this.fetchUserProfile();
    },
    methods: {
        getAnswer: function () {
            if (this.keyword === '') {
                this.items = null;
                this.message = '';
                return;
            }
        
            this.message = 'Loading...';
            var vm = this;
            var params = {
                page: this.currentPage,
                per_page: this.perPage,
                query: this.keyword
            };
        
            axios.get('https://qiita.com/api/v2/items', { params })
            .then(function (res) {
                console.log(res);
                console.log(res.data);
                console.log(vm.keyword);
                vm.items = res.data;
    
                if (res.data.length > 0) {
                    console.log(res.data.length);
                    console.log(vm.perPage);
                    vm.totalPages = Math.ceil(res.data.length / 20);
                    console.log(vm.totalPages);
                } else {
                    console.warn('No items found in the response.');
                }
            })
            .catch(function (error) {
                vm.message = 'Error!' + error;
            })
            .finally(function () {
                vm.message = '';
            });
        },
        openModal(item) {
            const userId = this.userProfile.user_id;
            axios.get(`php/gettagsDB.php?user_id=${userId}`)
                .then(response => {
                    this.tags = response.data.gettags;
                })
                .catch(error => {
                    console.error('Error fetching tags:', error);
                });
            this.selectedItem = item;
            this.comment = '';
        
            this.$nextTick(() => {
                const modal = document.querySelector('.modal');
                const modalBackground = document.querySelector('.modal-background');
        
                if (modal && modalBackground) {
                    modal.style.display = 'block';
                    modalBackground.style.display = 'block';
                }
            });
        },
        closeModal() {
            this.selectedItem = null;
            document.querySelector('.modal').style.display = 'none';
            document.querySelector('.modal-background').style.display = 'none';
        },
        linkSave() {
            console.log('保存が押されました');
            console.log('Title:', this.selectedItem.title);
            console.log('URL:', this.selectedItem.url);
            console.log('コメント:', this.comment);
            console.log('タグ:', this.selectedTag);
        
            const selectedItem = this.selectedItem;
        
            axios.post('php/bookmark.php', {
                user_id: this.userProfile.user_id,
                title: selectedItem.title,
                url: selectedItem.url,
                comment: this.comment,
                tag_id: this.selectedTag
            })
            .then(response => {
                alert(selectedItem.title + "をブックマークしました");
            })
            .catch(error => {
                console.error('Error saving bookmark:', error);
            })
            .finally(() => {
                this.closeModal();
            });
        },        
        fetchUserProfile() {
            axios.get('php/getUserProfile.php')
                .then(response => {
                    this.userProfile = response.data.userProfile; 
                })
                .catch(error => {
                });
        },
        goToProfile() {
            location.href = 'profile.html?user_id=' + this.userProfile.user_id;
        },
        goToBookmark() {
            location.href = 'bookmark.html?user_id=' + this.userProfile.user_id;
        },
        updatePerPage(newPerPage) {
            this.perPage = newPerPage;
            this.getAnswer();
        },
        changePage(newPage) {
            if (newPage >= 1 && newPage <= this.totalPages) {
                this.currentPage = newPage;
                this.getAnswer();
            }
        },
    }
});

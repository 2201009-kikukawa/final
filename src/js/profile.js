new Vue({
    el: '#profile-app',
    data() {
        return {
            userName: '',
            userEmail: '',
            userId: '',
            tags: [],
            tagname: '',
            tagscount: 0,
            selectedtag: false,
            message: ''
        }
    },
    mounted() {
        const userId = new URLSearchParams(window.location.search).get('user_id');

        if (userId) {
            axios.get(`php/getUserDB.php?user_id=${userId}`)
                .then(response => {
                    const userProfile = response.data.userProfile;
                    this.userName = userProfile.user_name;
                    this.userEmail = userProfile.mail;
                    this.userId = userProfile.user_id;
                })
                .catch(error => {
                    console.error('Error fetching user profile:', error);
                });
        }

        if (userId) {
            axios.get(`php/gettagsDB.php?user_id=${userId}`)
                .then(response => {
                    this.tags = response.data.gettags;
                    if (this.tags) {
                        this.tagscount = response.data.tagscount;
                    } else {
                        this.tagscount = 0;
                    }
                })
                .catch(error => {
                    console.error('Error fetching get tags:', error);
                });
        }
    },
    methods: {
        goBack() {
            window.history.back();
        },
        openModal() {
            this.selectedtag = true;
            const modal = document.querySelector('.modal');
            const modalBackground = document.querySelector('.modal-background');

            if (modal && modalBackground) {
                modal.style.display = 'block';
                modalBackground.style.display = 'block';
            }
        },
        closeModal() {
            this.selectedtag = false;
            document.querySelector('.modal').style.display = 'none';
            document.querySelector('.modal-background').style.display = 'none';
        },
        tagSave() {
            console.log('保存が押されました');
            axios.post('php/tagInsert.php', {
                userId: this.userId,
                tagname: this.tagname
            })
                .then(response => {
                    this.message = response.data.message;
                    alert(this.message);
                    location.reload();
                })
                .catch(error => {
                    alert("タグの作成に失敗しました");
                });
            this.closeModal();
        },
    }
});

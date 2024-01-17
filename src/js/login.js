    new Vue({
        el: '#app',
        data: {
            text1: "Login",
            text2: "メールアドレスまたはパスワードが間違っています。",
            text3: "ログイン",
            text4: "新規登録はこちらから",
            flag: false,
            email: '',
            username: '',
            password: '',
            message:''
        },
        methods: {
            toggleForm() {
                this.flag = !this.flag;
                if (this.flag) {
                    this.text1 = "Register";
                    this.text2 = "既に使われているメールアドレスです。"
                    this.text3 = "新規登録";
                    this.text4 = "ログインはこちらから";
                } else {
                    this.text1 = "Login";
                    this.text2 = "メールアドレスまたはパスワードが間違っています。"
                    this.text3 = "ログイン";
                    this.text4 = "新規登録はこちらから";
                }
            },
            submitForm() {
                if (this.flag) {
                    axios.post('php/register.php', {
                        email: this.email,
                        username: this.username,
                        password: this.password
                    })
                    .then(response => {
                        this.message = response.data.message;
                        alert(this.message); 
                        location.reload();
                    })
                    .catch(error => {
                        alert("ユーザーの追加に失敗しました");
                    });
                    console.log("Register form submitted to register.php");
                } else {
                    axios.post('php/login.php', {
                        email: this.email,
                        password: this.password
                    })
                    .then(response => {
                        if (response.data.message === 'ようこそ') {
                            alert(response.data.message);
                            location.href = 'search.html';
                        } else {
                            alert('ログインに失敗しました');
                            location.reload();
                        }
                    })
                    .catch(error => {
                        alert(this.text2);
                        location.reload();
                    });
                    
                    console.log("Login form submitted to login.php");
                }
            }
        }
    });


// 入力フォームを取得
let form_value = document.forms.password;

// 入力フォームの変化を監視
form_value.oninput = () => {
    let login_btn = document.forms.login.style;

    if (form_value.value != "")
    {
        login_btn.pointerEvents = "auto";
        login_btn.backgroundColor = "#eacd53";
        login_btn.color = "#111926";
    }
    else
    {
        login_btn.pointerEvents = "none";
        login_btn.backgroundColor = "#4d4d4d";
        login_btn.color = "#808080";
    }
}

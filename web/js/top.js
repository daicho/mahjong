var RankImg = new Array("/svg/rank_frame.svg", "/svg/rank_fill.svg");
var ManImg = new Array("/svg/man_frame.svg", "/svg/man_fill.svg");

function man_click() {
    document.getElementById("man_img").src = ManImg[1];
    document.getElementById("rank_img").src = RankImg[0];
}

function rank_click() {
    document.getElementById("man_img").src = ManImg[0];
    document.getElementById("rank_img").src = RankImg[1];
}

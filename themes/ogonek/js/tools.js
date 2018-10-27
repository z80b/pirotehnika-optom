function toOgonekCurrency(num) {
    var str = new String(num);
    return str.replace(/(\d+)([\.|\,])(\d+)(.*)/, "$1$2<em>$3</em>$4");
}
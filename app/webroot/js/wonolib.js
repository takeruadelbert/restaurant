function RP(rp) {
    if (rp == "") {
        return "Rp. 0";
    }
    while (/(\d+)(\d{3})/.test(rp.toString())) {
        rp = rp.toString().replace(/(\d+)(\d{3})/, '$1' + '.' + '$2');
    }
    return "Rp. " + rp + ",-";
}

function IDR(rp) {
    if (rp == "") {
        return "0";
    }
    while (/(\d+)(\d{3})/.test(rp.toString())) {
        rp = rp.toString().replace(/(\d+)(\d{3})/, '$1' + '.' + '$2');
    }
    return rp;
}

function nullToStrip(e) {
    if (e == null) {
        return "-";
    }
    return e;
}
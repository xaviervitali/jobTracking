export function titlelize(str) {
    if (!!str) {
        return str
    }
    str = str.slice(0, 1).toUpperCase() + str.toLowerCase().slice(1);


    return str
}

export function plural(count, singular, plural) {
    return Math.abs(count) > 1 ? plural : singular;
}
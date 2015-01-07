Ext.provide('Phlexible.Format');

Phlexible.Format = {
    size: function (size, binarySuffix) {
        if (!size) {
            return 0;
        }

        if (!binarySuffix || binarySuffix === undefined) {
            var suffix = ["Byte", "kB", "MB", "GB", "TB", "PB"];
            var divisor = 1000;
        } else {
            var suffix = ["Byte", "KiB", "MiB", "GiB", "TiB", "PiB"];
            var divisor = 1024;
        }
        var result = size;
        size = parseInt(size, 10);
        result = size + " " + suffix[0];
        var loop = 0;
        while (size / divisor > 1) {
            size = size / divisor;
            loop++;
        }
        result = Math.round(size) + " " + suffix[loop];

        return result;
    },

    date: function (date) {
        var newDate = "";
        if (date) {
            newDate = new Date(date).format('Y-m-d H:i:s');
        }
        return newDate;
    },

    age: function (time, items, noseconds) {
        if (!items) {
            items = 2;
        }

        if (!parseInt(time, 10)) {
            return '0s';
        }

        var msuffix = [
            Phlexible.gui.Strings.seconds,
            Phlexible.gui.Strings.minutes,
            Phlexible.gui.Strings.hours,
            Phlexible.gui.Strings.days,
            Phlexible.gui.Strings.weeks,
            Phlexible.gui.Strings.months,
            Phlexible.gui.Strings.years
        ];
        var ssuffix = [
            Phlexible.gui.Strings.second,
            Phlexible.gui.Strings.minute,
            Phlexible.gui.Strings.hour,
            Phlexible.gui.Strings.day,
            Phlexible.gui.Strings.week,
            Phlexible.gui.Strings.month,
            Phlexible.gui.Strings.year
        ];

        var result = '';
        var results = [];
        var loop = 6;
        var dummy = '';
        var v, m;

//debugger;

        // year
        m = (60 * 60 * 24 * 30 * 12);
        if (items && time > m) {
            items--;
            v = parseInt(time / m, 10);
            results.push(v + ' ' + (v == 1 ? ssuffix[loop] : msuffix[loop]));
            time = parseInt(time % m, 10);
        }
        loop--;

        // month
        m = (60 * 60 * 24 * 30);
        if (items && time > m) {
            items--;
            v = parseInt(time / m, 10);
            results.push(v + ' ' + (v == 1 ? ssuffix[loop] : msuffix[loop]));
            time = parseInt(time % m, 10);
        }
        loop--;

        // week
        m = (60 * 60 * 24 * 7);
        if (items && time > m) {
            items--;
            v = parseInt(time / m, 10);
            results.push(v + ' ' + (v == 1 ? ssuffix[loop] : msuffix[loop]));
            time = parseInt(time % m, 10);
        }
        loop--;

        // day
        m = (60 * 60 * 24);
        if (items && time > m) {
            items--;
            v = parseInt(time / m, 10);
            results.push(v + ' ' + (v == 1 ? ssuffix[loop] : msuffix[loop]));
            time = parseInt(time % m, 10);
        }
        loop--;

        // hour
        m = (60 * 60);
        if (items && time > m) {
            items--;
            v = parseInt(time / m, 10);
            results.push(v + ' ' + (v == 1 ? ssuffix[loop] : msuffix[loop]));
            time = parseInt(time % m, 10);
        }
        loop--;

        // minute
        m = (60);
        if (items && time > m) {
            items--;
            v = parseInt(time / m, 10);
            results.push(v + ' ' + (v == 1 ? ssuffix[loop] : msuffix[loop]));
            time = parseInt(time % m, 10);
        }
        loop--;

        // second
        if (items && !noseconds) {
            v = parseInt(time, 10);
            results.push(v + ' ' + (v == 1 ? ssuffix[loop] : msuffix[loop]));
        }

        if (!results.length) {
            return '-';
        }

        if (results.length == 1) {
            return results.pop();
        }

        while (results.length > 3) {
            results.pop();
        }

        results.reverse();

        while (results.length > 1) {
            v = results.pop();
            result += (result ? ', ' : '') + v;
        }

        result += ' ' + Phlexible.gui.Strings.and + ' ' + results.pop();

        return result;

        //return result.trim();
    }
};

/*jsl:ignoreall*/
// Create user extensions namespace (Ext.ux)
Ext.namespace('Ext.ux');

Ext.provide('Ext.ux.PasswordField');

/**
 * Ext.ux.PasswordField Extension Class
 *
 * @author Benjamin Horan (benh@b2bwebsolutions.com.au)
 * @version 0.21
 *
 * @license MIT License: http://www.opensource.org/licenses/mit-license.php
 *
 * Combines the existing work of:
 *     CAPS detection code by James Asher
 *     (http://17thdegree.com/archives/2007/12/06/capturing-caps-lock-with-the-ext-js-framework/)
 *        - Initial concept by Stuart Langridge
 *               http://24ways.org/2007/capturing-caps-lock
 *  and
 *     Password Strength UX code by Eelco Wiersma
 *     (http://testcases.pagebakers.com/PasswordMeter/)
 *        - Algorithm based on code of Tane
 *            http://digitalspaghetti.me.uk/index.php?q=jquery-pstrength
 *      and
 *        - Steve Moitozo
 *            http://www.geekwisdom.com/dyn/passwdmeter
 * --------------------------------------------------------
 *
 * @class Ext.ux.PasswordField
 * @extends Ext.form.Textfield
 * @constructor
 * @param {Object} config Configuration options
 *
 * EXT Version: 2.0
 *
 * Refer to config options for Ext.form.TextField
 *
 * showCapsWarning : boolean -
 *    If 'true', will show  warning message beside password
 *    field if CAPS LOCK is detected.
 *
 * showStrengthMeter : boolean -
 *    If 'true', will show password strength meter immediately
 *    below password field.
 *
 * pwStrengthMeter :  function(password [string]) -
 *    If set, must point to a function that recieves the password
 *    as a string, performs strength ratings on it, and returns an
 *    integer value between 0 and 100 (0 = weakest, 100 = strongest)
 *
 * pwStrengthMeterCls : string -
 *    CSS Class to apply to the password strength meter.
 *
 * pwStrengthMeterFocusCls : string -
 *    CSS Class to apply to strength meter when the password field has focus.
 *
 * pwStrengthMeterScoreBarCls : string -
 *    CSS Class to apply to the score bar within the pw strength meter.
 *
 */

Ext.ux.PasswordField = function (config) {
    if (!config) config = {};
    // call parent constructor
    Ext.ux.PasswordField.superclass.constructor.call(this, config);

    // set custom config properties (or assume component defaults)
    this.showCapsWarning = config.showCapsWarning || true;
    this.showStrengthMeter = config.showStrengthMeter || false;
    this.pwStrengthTest = config.pwStrengthTest || this.calcStrength;
    this.pwStrengthMeterCls = config.pwStrengthMeterCls || 'x-form-password-strengthMeter';
    this.pwStrengthMeterFocusCls = config.pwStrengthMeterFocusCls || 'x-form-password-strengthMeter-focus';
    this.pwStrengthScoreBarCls = config.pwStrengthScoreBarCls || 'x-form-password-scoreBar';
};

Ext.extend(Ext.ux.PasswordField, Ext.form.TextField, {
        inputType: 'password',
        // private
        onRender: function (ct, position) {
            Ext.ux.PasswordField.superclass.onRender.call(this, ct, position);

            // create caps lock warning box
            if (this.showCapsWarning) {
                var id = Ext.id();
                this.alertBox = Ext.DomHelper.append(document.body, {
                    tag: 'div',
                    style: 'width: 8em; z-index: 9500;',
                    children: [
                        {
                            tag: 'div',
                            style: 'text-align: center; color: red;',
                            html: 'Caps Lock.',
                            id: id
                        }
                    ]
                }, true);
                Ext.fly(id).boxWrap();
                this.alertBox.hide();
            }
            // create password strength meter
            if (this.showStrengthMeter) {
                this.objMeter = ct.createChild({tag: "div", 'class': this.pwStrengthMeterCls});
                this.objMeter.setWidth(ct.first('INPUT').getWidth(false));
                this.scoreBar = this.objMeter.createChild({tag: "div", 'class': this.pwStrengthScoreBarCls});
                if (Ext.isIE && !Ext.isIE7) { // Fix style for IE6
                    this.objMeter.setStyle('margin-left', '3px');
                }
            }
        },
        afterRender: function () {
            Ext.ux.PasswordField.superclass.afterRender.call(this);
            this.objMeter.setWidth(this.el.getWidth(false));
            //alert('after render! ' + this.el.getWidth(false) + ', ' + this.objMeter.getWidth(false));
        },
        initEvents: function () {
            Ext.ux.PasswordField.superclass.initEvents.call(this);

            this.el.on('keypress', this.handleKeypress, this);
            this.el.on('blur', this.handleBlur, this);
            this.el.on('focus', this.handleFocus, this);
            this.el.on('keyup', this.handleKeyUp, this);
        },
        handleFocus: function (e) {
            if (!Ext.isOpera) { // don't touch in Opera
                this.objMeter.addClass(this.pwStrengthMeterFocusCls);
            }
        },
        handleBlur: function (e) {
            if (!Ext.isOpera) { // don't touch in Opera
                this.objMeter.removeClass(this.pwStrengthMeterFocusCls);
            }
            if (this.showCapsWarning) {
                this.hideCapsMessage();
            }
        },
        handleKeypress: function (e) {
            var charCode = e.getCharCode();
            // blank field if ESC pressed
            if (charCode == e.ESC) {
                this.setRawValue('');
            }
            // detect if CAPS LOCK is on and show warning if appropriate
            if (this.showCapsWarning) {
                if (
                    (e.shiftKey && charCode >= 97 && charCode <= 122) ||
                        (!e.shiftKey && charCode >= 65 && charCode <= 90)
                    ) {
                    this.showCapsMessage(e.target);
                } else {
                    this.hideCapsMessage();
                }
            }
        },
        handleKeyUp: function (e) {
            if (this.showStrengthMeter) {

                this.updateMeter(e);
            }
        },
        showCapsMessage: function (el) {
            // set position of caps warning based on whether field is stand-alone
            // or has strength meter immediately below it.
            var position = this.showStrengthMeter ? 'tl-tr' : 'l-r';
            this.alertBox.alignTo(el, position, [5, 0]);
            this.alertBox.show();
        },
        hideCapsMessage: function () {
            this.alertBox.hide();
        },
        /**
         * Sets the width of the meter, based on the score
         * @param {Object} e
         * Private function
         */
        updateMeter: function (e) {
            var score = 0
            var p = e.target.value;

            var maxWidth = this.objMeter.getWidth() - 2;

            var nScore = this.pwStrengthTest(p);

            if (nScore > 100) {
                nScore = 100;
            }

            var scoreWidth = (maxWidth / 100) * nScore;

            this.scoreBar.setWidth(scoreWidth, true);
        },
        /**
         * Calculates the strength of a password
         * @param {Object} p The password that needs to be calculated
         * @return {int} intScore The strength score of the password (max = 100)
         */
        calcStrength: function (p) {
            var intScore = 0;

            if (p.length == 0) return (intScore);

            // PASSWORD LENGTH
            intScore += p.length;

            if (p.length > 0 && p.length <= 4) {                    // length 4 or less
                intScore += p.length;
            }
            else if (p.length >= 5 && p.length <= 7) {	// length between 5 and 7
                intScore += 6;
            }
            else if (p.length >= 8 && p.length <= 15) {	// length between 8 and 15
                intScore += 12;
            }
            else if (p.length >= 16) {               // length 16 or more
                intScore += 18;
            }

            // LETTERS (Not exactly implemented as dictacted above because of my limited understanding of Regex)
            if (p.match(/[a-z]/)) {              // [verified] at least one lower case letter
                intScore += 1;
            }
            if (p.match(/[A-Z]/)) {              // [verified] at least one upper case letter
                intScore += 5;
            }
            // NUMBERS
            if (p.match(/\d/)) {             	// [verified] at least one number
                intScore += 5;
            }
            if (p.match(/.*\d.*\d.*\d/)) {            // [verified] at least three numbers
                intScore += 5;
            }

            // SPECIAL CHAR
            if (p.match(/[!,@,#,$,%,^,&,*,?,_,~]/)) {           // [verified] at least one special character
                intScore += 5;
            }
            // [verified] at least two special characters
            if (p.match(/.*[!,@,#,$,%,^,&,*,?,_,~].*[!,@,#,$,%,^,&,*,?,_,~]/)) {
                intScore += 5;
            }

            // COMBOS
            if (p.match(/(?=.*[a-z])(?=.*[A-Z])/)) {        // [verified] both upper and lower case
                intScore += 2;
            }
            if (p.match(/(?=.*\d)(?=.*[a-z])(?=.*[A-Z])/)) { // [verified] both letters and numbers
                intScore += 2;
            }
            // [verified] letters, numbers, and special characters
            if (p.match(/(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!,@,#,$,%,^,&,*,?,_,~])/)) {
                intScore += 2;
            }

            // return score (max = 100)
            return Math.round(intScore * 2);
        }
    }
);

Ext.reg('passwordfield', Ext.ux.PasswordField);

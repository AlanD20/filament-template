var __assign =
  (this && this.__assign) ||
  function () {
    __assign =
      Object.assign ||
      function (t) {
        for (var s, i = 1, n = arguments.length; i < n; i++) {
          s = arguments[i];
          for (var p in s)
            if (Object.prototype.hasOwnProperty.call(s, p)) t[p] = s[p];
        }
        return t;
      };
    return __assign.apply(this, arguments);
  };
import { Arabic as ar } from './ar';
import { Kurdish as ckb } from './ckb';
import { english as en } from './default';

var l10n = {
  ar: ar,
  ckb: ckb,
  en: en,
  default: __assign({}, en),
};
export default l10n;

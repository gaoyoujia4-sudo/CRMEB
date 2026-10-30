import Cookies from 'js-cookie';

const TOKEN_KEY = 'supplier_token';
const INFO_KEY = 'supplier_info';

export const getToken = () => Cookies.get(TOKEN_KEY) || '';

export const setToken = (token, expTime) => {
  const days = expTime ? Math.max(1, (expTime - Math.round(Date.now() / 1000)) / 86400) : 1;
  Cookies.set(TOKEN_KEY, token, { expires: days });
};

export const removeToken = () => {
  Cookies.remove(TOKEN_KEY);
  Cookies.remove(INFO_KEY);
};

export const getSupplierInfo = () => {
  const str = Cookies.get(INFO_KEY) || '';
  try {
    return str ? JSON.parse(str) : {};
  } catch (e) {
    return {};
  }
};

export const setSupplierInfo = (info) => {
  Cookies.set(INFO_KEY, JSON.stringify(info || {}), { expires: 1 });
};

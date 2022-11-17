const pages = {};

const base_url = "//litegram.localhost/";

pages.load = (page) => {
  eval("pages.load_" + page + "();");
};

pages.is_logged_in = () => {
  if (localStorage.getItem("token") != null) {
    window.location.href = "index.html";
    return true;
  } else return false;
};

pages.is_logged_out = () => {
  if (localStorage.getItem("token") == null) {
    window.location.href = "login.html";
    return true;
  } else return false;
};

pages.unhide = () => {
  const container = document.getElementById("container");
  container.classList.remove("hidden");
};

pages.post = async (url, data, token = null) => {
  try {
    return await axios.post(url, data, {
      headers: {
        Accept: "application/json",
        "Content-Type": "application/json",
        Authorization: "Bearer " + token,
      },
    });
  } catch (error) {
    return null;
  }
};

pages.load_login = async () => {
  if (pages.is_logged_in()) return;
  pages.unhide();

  const login = document.getElementById("login");
  const username = document.getElementById("username");
  const password = document.getElementById("password");
  const error = document.getElementById("error");

  login.addEventListener("click", (e) => {
    e.preventDefault();

    let data = new FormData();
    data.append("username", username.value);
    data.append("password", password.value);

    const url = base_url + "login.php";
    pages.post(url, data).then((response) => {
      if (response.data.success) {
        localStorage.setItem("token", response.data.token);
        window.location.href = "index.html";
      } else {
        error.innerText = response.data.message;
      }
    });
  });
};

pages.load_signup = async () => {
  if (pages.is_logged_in()) return;
  pages.unhide();

  const login = document.getElementById("signup");
  const f_name = document.getElementById("f_name");
  const l_name = document.getElementById("l_name");
  const email = document.getElementById("email");
  const username = document.getElementById("username");
  const password = document.getElementById("password");
  const error = document.getElementById("error");

  login.addEventListener("click", (e) => {
    e.preventDefault();

    let data = new FormData();
    data.append("f_name", f_name.value);
    data.append("l_name", l_name.value);
    data.append("email", email.value);
    data.append("username", username.value);
    data.append("password", password.value);

    const url = base_url + "register.php";
    pages.post(url, data).then((response) => {
      if (response.data.success) {
        localStorage.setItem("token", response.data.token);
        window.location.href = "index.html";
      } else {
        error.innerText = response.data.message;
      }
    });
  });
};

pages.load_home = async () => {
  if (pages.is_logged_out()) return;
  pages.unhide();
};

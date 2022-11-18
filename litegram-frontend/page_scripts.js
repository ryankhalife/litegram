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
  const container = document.getElementById("main-container");
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

pages.get = async (url, token = null) => {
  try {
    return await axios.get(url, {
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

  const token = localStorage.getItem("token");
  const feed = await pages.get(base_url + "get_feed.php", token);

  if (!feed.data.success) return;

  const feed_container = document.getElementById("feed-container");
  feed.data.posts.forEach(async (post) => {
    let res = await pages.get(base_url + "user.php?id=" + post.user_id);
    const user = res.data.user;
    const user_profile_picture = user.profile_picture;
    const user_username = user.username;
    res = await pages.get(base_url + "get_likes.php?id=" + post.id);
    const likes = res.data.likes;
    const like_text = likes == 1 ? "like" : "likes";

    const div = document.createElement("div");
    div.classList.add("image-post");
    div.innerHTML = `
      <div class="post-header">
        <div class="post-user">
          <img class="post-user-img" src="${base_url}/uploads/profile-pictures/${user_profile_picture}" alt="Profile Picture" />
          <div class="post-user-name bold">${user_username}</div>
        </div>
      </div>
      <div class="post-image">
        <img src="${base_url}/uploads/posts/${post.image}" alt="Post image" />
      </div>
      <div class="post-info">
        <div class="post-likes bold">${likes} ${like_text}</div>
        <div class="post-caption">
          <div class="post-caption-user bold">${user_username}</div>
          <div class="post-caption-text">${post.caption}</div>
        </div>
        <div class="post-comments"></div>
      </div>
      <div class="post-add-comment">
        <input type="text" name="comment" id="comment" placeholder="Add a comment..." />
        <button disabled class="post-add-comment-btn">Post</button>
      </div>
`;

    div.querySelector(".post-add-comment input").addEventListener("input", (e) => {
      const btn = div.querySelector(".post-add-comment button");
      if (e.target.value == "") {
        btn.disabled = true;
      } else {
        btn.disabled = false;
      }
    });

    feed_container.appendChild(div);
  });

  const posts = document.querySelectorAll(".image-post");
  console.log(posts);

  const logoutHandler = () => {
    localStorage.removeItem("token");
    window.location.href = "login.html";
  };

  const logout = document.getElementById("logout");
  logout.addEventListener("click", logoutHandler);
};

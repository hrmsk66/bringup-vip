<nav class="navbar has-shadow">
    <div class="navbar-brand">
        <a class="navbar-item">
            <img src="images/logo.png">
        </a>
    </div>
    <div class="navbar-menu">
        <div class="navbar-start">
            <div class="navbar-item">
                <small>Lab Bring Up Helper</small>
            </div>
        </div>
    </div>
</nav>

<div class="tabs is-fullwidth">
    <ul>
        <router-link tag="li" to="/rootcert">
            <a>Install Root Certificate</a>
        </router-link>
        <router-link tag="li" to="/csr">
            <a>Issue Certificate</a>
        </router-link>
        <router-link tag="li" to="/activate">
            <a>Activate vEdge</a>
        </router-link>
    </ul>
</div>

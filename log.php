<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Foodie Signup | Create Account</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: system-ui, 'Segoe UI', 'Poppins', 'Inter', -apple-system, BlinkMacSystemFont, 'Roboto', sans-serif;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(145deg, #f8f5e9 0%, #e9e0c9 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 1.5rem;
        }

        /* main card container */
        .signup-container {
            max-width: 520px;
            width: 100%;
            background: rgba(255, 255, 245, 0.98);
            backdrop-filter: blur(0px);
            border-radius: 3rem;
            box-shadow: 0 25px 45px -12px rgba(0, 0, 0, 0.35), 0 4px 12px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            transition: all 0.2s ease;
            border: 1px solid rgba(230, 190, 120, 0.4);
        }

        /* food inspired header */
        .food-header {
            background: #2d2b1f;
            padding: 1.6rem 2rem 1.2rem;
            text-align: center;
            position: relative;
        }

        .food-header h1 {
            font-size: 2rem;
            font-weight: 700;
            letter-spacing: -0.3px;
            background: linear-gradient(135deg, #FFE6B0, #FFB347);
            background-clip: text;
            -webkit-background-clip: text;
            color: transparent;
            text-shadow: 0 2px 3px rgba(0,0,0,0.1);
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .food-header h1::before {
            content: "🍽️";
            font-size: 1.8rem;
            background: none;
            -webkit-background-clip: unset;
            color: #FFB347;
            text-shadow: none;
        }

        .food-header h1::after {
            content: "🥗";
            font-size: 1.8rem;
            background: none;
            -webkit-background-clip: unset;
            color: #FFB347;
        }

        .food-tagline {
            color: #cfc7ae;
            font-size: 0.85rem;
            margin-top: 8px;
            font-weight: 400;
            letter-spacing: 0.3px;
        }

        /* form area */
        .form-panel {
            padding: 2rem 2rem 1.8rem;
        }

        .input-group {
            margin-bottom: 1.35rem;
            display: flex;
            flex-direction: column;
        }

        .input-group label {
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #5a4a2e;
            margin-bottom: 0.4rem;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .input-group label i {
            font-style: normal;
            font-size: 1.05rem;
        }

        .input-group input {
            width: 100%;
            padding: 0.9rem 1rem;
            font-size: 1rem;
            background: #fffef7;
            border: 1.5px solid #e5d5b0;
            border-radius: 1.75rem;
            transition: all 0.2s;
            outline: none;
            color: #2c281c;
            font-weight: 500;
        }

        .input-group input:focus {
            border-color: #f5a623;
            box-shadow: 0 0 0 3px rgba(245, 166, 35, 0.2);
            background: #ffffff;
        }

        .input-group input::placeholder {
            color: #c7bb97;
            font-weight: 400;
            font-size: 0.9rem;
        }

        /* name row (first + last) */
        .name-row {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .name-row .input-group {
            flex: 1;
            min-width: 120px;
        }

        /* password hint */
        .password-hint {
            font-size: 0.7rem;
            color: #8f7a58;
            margin-top: 5px;
            margin-left: 0.7rem;
        }

        /* button */
        .signup-btn {
            background: #2b2b1a;
            color: #ffefcf;
            width: 100%;
            padding: 0.9rem;
            font-size: 1.1rem;
            font-weight: 700;
            border: none;
            border-radius: 2.5rem;
            margin-top: 1rem;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .signup-btn:hover {
            background: #46442c;
            transform: translateY(-2px);
            box-shadow: 0 12px 18px -8px rgba(0,0,0,0.2);
        }

        .signup-btn:active {
            transform: translateY(1px);
        }

        /* status messages */
        .status-area {
            margin: 0.8rem 0 0;
            padding: 0 0.2rem;
            min-height: 3rem;
        }

        .message {
            padding: 0.7rem 1rem;
            border-radius: 2rem;
            font-size: 0.85rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
            background: #f2eee2;
            border-left: 4px solid;
        }

        .message.success {
            background: #e9f5e6;
            border-left-color: #2e7d32;
            color: #1f5423;
        }

        .message.error {
            background: #fff0ef;
            border-left-color: #c7362b;
            color: #a11e14;
        }

        .message.info {
            background: #eef2fa;
            border-left-color: #3b6cb7;
            color: #1e3c6b;
        }

        /* DB viewer section */
        .db-viewer {
            margin: 0 2rem 2rem 2rem;
            background: #fbf8ef;
            border-radius: 1.5rem;
            padding: 1rem 1.2rem;
            border: 1px solid #e9ddbc;
            box-shadow: inset 0 1px 3px #0001, 0 2px 4px #0001;
        }

        .db-header {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            flex-wrap: wrap;
            margin-bottom: 12px;
            gap: 8px;
        }

        .db-header h3 {
            font-size: 0.9rem;
            font-weight: 700;
            color: #4f3e24;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .db-actions {
            display: flex;
            gap: 10px;
        }

        .db-btn {
            background: #e6dabc;
            border: none;
            font-size: 0.7rem;
            padding: 5px 10px;
            border-radius: 40px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.1s;
            color: #3e2f1b;
        }

        .db-btn.clear-btn {
            background: #ffddd3;
            color: #b33;
        }

        .db-btn:hover {
            background: #dacfaa;
            transform: scale(0.97);
        }

        .user-list {
            max-height: 210px;
            overflow-y: auto;
            font-size: 0.8rem;
        }

        .user-card {
            background: white;
            border-radius: 1rem;
            padding: 0.6rem 0.8rem;
            margin-bottom: 8px;
            border-left: 4px solid #f5a623;
            box-shadow: 0 1px 2px #0001;
            font-family: monospace;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 6px;
        }

        .user-info {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            row-gap: 5px;
            align-items: baseline;
        }

        .user-username {
            font-weight: 800;
            color: #2c2a1f;
        }

        .user-name {
            color: #886e42;
        }

        .user-badge {
            font-size: 0.7rem;
            background: #f0e5d2;
            padding: 2px 8px;
            border-radius: 30px;
        }

        .empty-db {
            text-align: center;
            color: #b7a26b;
            padding: 1rem;
            font-style: italic;
        }

        hr {
            border: none;
            border-top: 2px dotted #e7dbbb;
            margin: 10px 0;
        }

        footer {
            text-align: center;
            font-size: 0.7rem;
            padding: 0.8rem 1rem 1.2rem;
            color: #b7a77c;
        }
    </style>
</head>
<body>
<div class="signup-container">
    <div class="food-header">
        <h1>FoodieVault</h1>
        <div class="food-tagline">savor your identity · join the feast</div>
    </div>

    <div class="form-panel">
        <form id="signupForm" action="javascript:void(0);">
            <!-- username -->
            <div class="input-group">
                <label><i>👤</i> Username *</label>
                <input type="text" id="username" name="username" placeholder="e.g., tasty_chef" autocomplete="off" required>
            </div>

            <!-- first + last name row -->
            <div class="name-row">
                <div class="input-group">
                    <label><i>🍎</i> First name *</label>
                    <input type="text" id="firstName" name="firstName" placeholder="First name" autocomplete="given-name" required>
                </div>
                <div class="input-group">
                    <label><i>🥑</i> Last name *</label>
                    <input type="text" id="lastName" name="lastName" placeholder="Last name" autocomplete="family-name" required>
                </div>
            </div>

            <!-- password -->
            <div class="input-group">
                <label><i>🔒</i> Password *</label>
                <input type="password" id="password" name="password" placeholder="create a strong password" autocomplete="new-password" required>
                <div class="password-hint">🔐 min. 4 characters (foodie secret)</div>
            </div>

            <button type="submit" class="signup-btn">
                <span>🍲</span> Sign<span>🍜</span>
            </button>
        </form>

        <div class="status-area" id="statusMessage"></div>
    </div>

    <!-- Food DB display (persistent storage simulation) -->
    <div class="db-viewer">
        <div class="db-header">
            <h3>🍱 FOOD DATABASE · registered foodies</h3>
            <div class="db-actions">
                <button id="exportDBBtn" class="db-btn" title="Export JSON">📦 Export</button>
                <button id="clearAllBtn" class="db-btn clear-btn" title="Delete all records">🗑️ Clear DB</button>
            </div>
        </div>
        <div id="usersListContainer" class="user-list">
            <!-- dynamic user cards appear here -->
            <div class="empty-db">✨ No foodies yet — be the first to sign up ✨</div>
        </div>
        <hr>
        <div style="font-size: 0.7rem; color:#a0906e; text-align:center;">✅ Data saved in browser (localStorage) as "foodie_users"</div>
    </div>
    <footer>🍕 every new member adds flavor to our community 🥘</footer>
</div>

<script>
    // ---------- FOOD DATABASE (localStorage based) ----------
    const STORAGE_KEY = "foodie_users";

    // load users from localStorage (food DB)
    function loadUserDatabase() {
        const raw = localStorage.getItem(STORAGE_KEY);
        if (!raw) return [];
        try {
            const users = JSON.parse(raw);
            // basic validation: must be array
            if (Array.isArray(users)) return users;
            return [];
        } catch(e) {
            console.warn("DB parse error", e);
            return [];
        }
    }

    // save entire users array to localStorage (food DB)
    function saveUserDatabase(users) {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(users));
        renderUserList();    // refresh UI display
    }

    // helper: check if username already exists (case-insensitive)
    function isUsernameTaken(username, currentUsers) {
        return currentUsers.some(user => user.username.toLowerCase() === username.trim().toLowerCase());
    }

    // add new user to food DB
    function addUserToDB(username, firstName, lastName, password) {
        const users = loadUserDatabase();
        const trimmedUsername = username.trim();
        const trimmedFirst = firstName.trim();
        const trimmedLast = lastName.trim();
        
        // validation: no empty fields
        if (!trimmedUsername || !trimmedFirst || !trimmedLast || !password.trim()) {
            return { success: false, message: "❌ All fields are required (username, first name, last name, password)." };
        }
        
        if (password.trim().length < 4) {
            return { success: false, message: "🔐 Password must be at least 4 characters long." };
        }
        
        if (isUsernameTaken(trimmedUsername, users)) {
            return { success: false, message: `⚠️ Username "${trimmedUsername}" is already taken. Try another tasty name!` };
        }
        
        // create new user object (password stored in plaintext for demo purposes; In real apps always hash!)
        const newUser = {
            id: Date.now() + Math.random().toString(36).substring(2, 6),
            username: trimmedUsername,
            firstName: trimmedFirst,
            lastName: trimmedLast,
            password: password.trim(),     // note: store hashed in production
            createdAt: new Date().toISOString()
        };
        
        users.push(newUser);
        saveUserDatabase(users);
        return { success: true, message: `🎉 Welcome ${trimmedFirst} ${trimmedLast}! Account created successfully.`, user: newUser };
    }
    
    // render all users in the "Food DB" panel
    function renderUserList() {
        const users = loadUserDatabase();
        const container = document.getElementById("usersListContainer");
        if (!container) return;
        
        if (users.length === 0) {
            container.innerHTML = `<div class="empty-db">🍽️ No foodies yet — be the first to sign up 🍽️</div>`;
            return;
        }
        
        // sort by newest first (id / timestamp)
        const sorted = [...users].reverse();
        const cardsHTML = sorted.map(user => {
            // mask password for display (only show hint)
            const maskedPass = '•'.repeat(Math.min(user.password.length, 8));
            return `
                <div class="user-card">
                    <div class="user-info">
                        <span class="user-username">@${escapeHtml(user.username)}</span>
                        <span class="user-name">${escapeHtml(user.firstName)} ${escapeHtml(user.lastName)}</span>
                        <span class="user-badge">🔒 ${maskedPass}</span>
                    </div>
                    <small style="color:#b49464; font-size:0.7rem;">🍴 foodie</small>
                </div>
            `;
        }).join('');
        
        container.innerHTML = cardsHTML;
    }
    
    // simple XSS prevention
    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/[&<>]/g, function(m) {
            if (m === '&') return '&amp;';
            if (m === '<') return '&lt;';
            if (m === '>') return '&gt;';
            return m;
        }).replace(/[\uD800-\uDBFF][\uDC00-\uDFFF]/g, function(c) {
            return c;
        });
    }
    
    // show status message (auto clear after 4 seconds)
    let messageTimeout = null;
    function showMessage(text, type = "info") {
        const statusDiv = document.getElementById("statusMessage");
        if (!statusDiv) return;
        if (messageTimeout) clearTimeout(messageTimeout);
        
        const icon = type === "success" ? "✅" : (type === "error" ? "⚠️" : "ℹ️");
        statusDiv.innerHTML = `<div class="message ${type}"><span>${icon}</span> <span>${escapeHtml(text)}</span></div>`;
        
        messageTimeout = setTimeout(() => {
            if (statusDiv) statusDiv.innerHTML = "";
        }, 4500);
    }
    
    // handle signup form submission
    function handleSignup(event) {
        event.preventDefault();
        
        const usernameInput = document.getElementById("username");
        const firstNameInput = document.getElementById("firstName");
        const lastNameInput = document.getElementById("lastName");
        const passwordInput = document.getElementById("password");
        
        const username = usernameInput.value;
        const firstName = firstNameInput.value;
        const lastName = lastNameInput.value;
        const password = passwordInput.value;
        
        const result = addUserToDB(username, firstName, lastName, password);
        
        if (result.success) {
            // clear form fields on success (except keep optional? better UX clear)
            usernameInput.value = '';
            firstNameInput.value = '';
            lastNameInput.value = '';
            passwordInput.value = '';
            showMessage(result.message, "success");
            // re-focus username for next signup
            usernameInput.focus();
        } else {
            showMessage(result.message, "error");
        }
    }
    
    // Export DB as JSON file (food_db_export.json)
    function exportDatabaseToJSON() {
        const users = loadUserDatabase();
        if (users.length === 0) {
            showMessage("📭 Food DB is empty, nothing to export.", "info");
            return;
        }
        const dataStr = JSON.stringify(users, null, 2);
        const blob = new Blob([dataStr], { type: "application/json" });
        const url = URL.createObjectURL(blob);
        const a = document.createElement("a");
        a.href = url;
        a.download = `food_db_export_${new Date().toISOString().slice(0,19)}.json`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
        showMessage(`📁 Exported ${users.length} foodie record(s) to JSON.`, "success");
    }
    
    // Clear entire Food DB (with confirmation)
    function clearDatabase() {
        const users = loadUserDatabase();
        if (users.length === 0) {
            showMessage("🍃 Database already empty, nothing to clear.", "info");
            return;
        }
        const confirmClear = confirm("⚠️ PERMANENT ACTION: Are you sure you want to DELETE all user records from Food DB? This cannot be undone.");
        if (!confirmClear) return;
        
        localStorage.setItem(STORAGE_KEY, JSON.stringify([]));
        renderUserList();
        showMessage(`🗑️ Food database cleared. Removed ${users.length} user(s).`, "success");
    }
    
    // initial render and event binding
    document.addEventListener("DOMContentLoaded", () => {
        // render existing DB users
        renderUserList();
        
        const form = document.getElementById("signupForm");
        if (form) form.addEventListener("submit", handleSignup);
        
        const exportBtn = document.getElementById("exportDBBtn");
        if (exportBtn) exportBtn.addEventListener("click", exportDatabaseToJSON);
        
        const clearBtn = document.getElementById("clearAllBtn");
        if (clearBtn) clearBtn.addEventListener("click", clearDatabase);
        
        // Optional: Add demo hint if DB is empty, show a little seed?
        const users = loadUserDatabase();
        if (users.length === 0) {
            // not required, but add a friendly console note
            console.log("🍲 Food DB ready — signup form will persist records.");
        }
    });
</script>
</body>
</html>
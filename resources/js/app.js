let toastTimer;

function showToast(message) {
    const toast = document.getElementById('toast');

    if (! toast) {
        return;
    }

    toast.textContent = message;
    toast.classList.remove('hidden');

    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => toast.classList.add('hidden'), 2600);
}

function copyText(text) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(text).catch(() => {});
    }
}

function initConnect() {
    const form = document.querySelector('[data-connect]');

    if (! form) {
        return;
    }

    const input = form.querySelector('#api-key');
    const assistant = form.querySelector('#assistant');
    const connectBtn = form.querySelector('[data-connect-btn]');
    const urlRow = form.querySelector('[data-url-row]');
    const urlLabel = form.querySelector('[data-connector-url]');

    const labels = { claude: 'Claude', chatgpt: 'ChatGPT' };

    let connectorUrl = null;

    const buildClaudeUrl = (url) => 'https://claude.ai/customize/connectors?' + new URLSearchParams({
        modal: 'add-custom-connector',
        connectorName: 'Termii MCP',
        connectorUrl: url,
    }).toString();

    const mintUrl = async (key) => {
        const response = await fetch('/connect', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({ key }),
        });

        if (! response.ok) {
            throw new Error('request failed');
        }

        return (await response.json()).url;
    };

    assistant.addEventListener('change', () => {
        connectBtn.textContent = `Connect to ${labels[assistant.value]}`;
    });

    form.addEventListener('submit', async (event) => {
        event.preventDefault();

        const key = input.value.trim();

        if (! key) {
            showToast('Enter your Termii API key first.');
            input.focus();
            return;
        }

        const choice = assistant.value;
        const tab = window.open('about:blank', '_blank');

        connectBtn.disabled = true;
        connectBtn.textContent = 'Generating…';

        try {
            connectorUrl = await mintUrl(key);

            urlLabel.textContent = connectorUrl;
            urlRow.hidden = false;

            if (choice === 'claude') {
                tab.location.href = buildClaudeUrl(connectorUrl);
            } else {
                copyText(connectorUrl);
                tab.location.href = 'https://chatgpt.com/#settings/Connectors';
                showToast('Connector URL copied. Paste it into ChatGPT’s connector settings.');
            }
        } catch (error) {
            if (tab) {
                tab.close();
            }
            showToast('Could not generate the connector link. Try again.');
        } finally {
            connectBtn.disabled = false;
            connectBtn.textContent = `Connect to ${labels[choice]}`;
        }
    });

    form.querySelectorAll('[data-copy-connector]').forEach((element) => {
        element.addEventListener('click', () => {
            if (! connectorUrl) {
                return;
            }

            copyText(connectorUrl);
            showToast('Connector URL copied. Paste it into your assistant.');
        });
    });
}

initConnect();

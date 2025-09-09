from flask import Flask, request, jsonify
import time
import random
import logging

app = Flask(__name__)

# ===================================================
# Lokitusasetukset
# ===================================================
logging.basicConfig(level=logging.INFO, format='%(asctime)s [%(levelname)s] %(message)s')

# ===================================================
# Root route testaukseen
# ===================================================
@app.route('/', methods=['GET'])
def index():
    return "Mock API is running", 200

# ===================================================
# Tilausten vastaanotto
# ===================================================
@app.route('/orders', methods=['POST'])
def orders():
    try:
        # ===================================================
        # Simuloidaan satunnaista viivettä 1–30 sekuntia
        # ===================================================
        delay = random.randint(1, 30)
        logging.info(f"Simulating delay: {delay} seconds")
        time.sleep(delay)

        # ===================================================
        # Satunnaisia virheitä 20% todennäköisyydellä
        # ===================================================
        if random.random() < 0.2:
            logging.warning("Simulated 500 Internal Server Error")
            return jsonify({"success": False, "error": "Internal Server Error"}), 500

        # ===================================================
        # Vastaanotetaan data
        # ===================================================
        data = request.get_json()
        logging.info(f"Mock order received: {data}")

        return jsonify({
            "success": True,
            "message": "Mock order received",
            "received": data
        }), 200

    except Exception as e:
        logging.error(f"Unexpected error: {e}")
        return jsonify({"success": False, "error": str(e)}), 500

# ===================================================
# Käynnistys
# ===================================================
if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000)

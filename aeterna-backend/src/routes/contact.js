const express = require("express");
const pool = require("../db/connection");

const router = express.Router();

// Validation helper
function validateContact(body) {
  const errors = [];
  const name = (body.name || "").trim();
  const email = (body.email || "").trim();
  const message = (body.message || "").trim();

  if (!name || name.length > 100) errors.push("Name is required (max 100 chars).");
  if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email) || email.length > 255)
    errors.push("A valid email is required (max 255 chars).");
  if (!message || message.length > 5000) errors.push("Message is required (max 5000 chars).");

  return { name, email, message, errors };
}

// POST /api/contact — submit a message
router.post("/", async (req, res) => {
  const { name, email, message, errors } = validateContact(req.body);

  if (errors.length) return res.status(400).json({ success: false, errors });

  try {
    const [result] = await pool.execute(
      "INSERT INTO contacts (name, email, message) VALUES (?, ?, ?)",
      [name, email, message]
    );
    res.status(201).json({ success: true, id: result.insertId });
  } catch (err) {
    console.error("DB error:", err.message);
    res.status(500).json({ success: false, errors: ["Server error. Please try again later."] });
  }
});

// GET /api/contact — list all messages (admin use)
router.get("/", async (_req, res) => {
  try {
    const [rows] = await pool.execute("SELECT * FROM contacts ORDER BY created_at DESC");
    res.json({ success: true, data: rows });
  } catch (err) {
    console.error("DB error:", err.message);
    res.status(500).json({ success: false, errors: ["Server error."] });
  }
});

module.exports = router;

require("dotenv").config();

const express = require("express");
const cors = require("cors");
const helmet = require("helmet");
const rateLimit = require("express-rate-limit");
const contactRoutes = require("./routes/contact");

const app = express();
const PORT = process.env.PORT || 3001;

// Security & parsing
app.use(helmet());
app.use(cors({ origin: process.env.CORS_ORIGIN || "*" }));
app.use(express.json({ limit: "16kb" }));

// Rate limiting for contact endpoint
app.use(
  "/api/contact",
  rateLimit({ windowMs: 15 * 60 * 1000, max: 20, message: { success: false, errors: ["Too many requests."] } })
);

// Routes
app.use("/api/contact", contactRoutes);

// Health check
app.get("/api/health", (_req, res) => res.json({ status: "ok" }));

app.listen(PORT, () => console.log(`Aeterna API running on http://localhost:${PORT}`));

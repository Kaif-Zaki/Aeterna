import { Moon, Sun } from "lucide-react";

interface ThemeToggleProps {
  theme: "light" | "dark";
  toggleTheme: () => void;
}

const ThemeToggle = ({ theme, toggleTheme }: ThemeToggleProps) => (
  <button
    onClick={toggleTheme}
    className="p-2 rounded-full transition-colors duration-300 hover:bg-secondary"
    aria-label="Toggle theme"
  >
    {theme === "light" ? <Moon size={18} /> : <Sun size={18} />}
  </button>
);

export default ThemeToggle;

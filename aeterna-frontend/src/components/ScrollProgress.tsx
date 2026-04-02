import { useScrollProgress } from "@/hooks/useScrollProgress";

const ScrollProgress = () => {
  const progress = useScrollProgress();
  return (
    <div
      className="scroll-progress"
      style={{ transform: `scaleX(${progress})` }}
    />
  );
};

export default ScrollProgress;

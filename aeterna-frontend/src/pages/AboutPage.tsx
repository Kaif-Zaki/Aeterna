import AnimatedSection from "@/components/AnimatedSection";
import PageTransition from "@/components/PageTransition";
import aboutImg from "@/assets/about-story.jpg";
import founderImg from "@/assets/founder.jpg";

const timeline = [
  { year: "2018", text: "Founded in a small atelier in Milan with a vision for timeless design." },
  { year: "2019", text: "First capsule collection launched to critical acclaim." },
  { year: "2021", text: "Expanded into menswear and sustainable fabric sourcing." },
  { year: "2023", text: "Global presence with flagship showrooms in Paris and Tokyo." },
  { year: "2025", text: "Launched the Aeterna Foundation for emerging designers." },
];

const AboutPage = () => (
  <PageTransition>
    <div className="pt-28 pb-16">
      {/* Hero */}
      <section className="container-luxury mb-24">
        <AnimatedSection>
          <h1 className="font-display text-4xl md:text-7xl tracking-[0.1em] uppercase mb-8">Our Story</h1>
          <p className="text-muted-foreground text-lg max-w-2xl leading-relaxed">
            Aeterna was born from a belief that fashion should transcend seasons. We craft pieces that feel as relevant
            tomorrow as they do today — garments that age with grace.
          </p>
        </AnimatedSection>
      </section>

      {/* Image + Story */}
      <section className="container-luxury grid grid-cols-1 lg:grid-cols-2 gap-12 mb-24">
        <AnimatedSection>
          <div className="image-zoom">
            <img src={aboutImg} alt="Aeterna atelier" className="w-full h-full object-cover" loading="lazy" width={1200} height={800} />
          </div>
        </AnimatedSection>
        <AnimatedSection delay={0.2}>
          <div className="flex flex-col justify-center">
            <h2 className="font-display text-3xl md:text-4xl tracking-wider uppercase mb-6">Mission & Vision</h2>
            <p className="text-muted-foreground leading-relaxed mb-4">
              Our mission is to create clothing that respects both the wearer and the world. We source the finest
              sustainable materials and collaborate with master artisans who share our dedication to craft.
            </p>
            <p className="text-muted-foreground leading-relaxed">
              We envision a future where fashion is conscious, purposeful, and enduring — where every thread carries meaning.
            </p>
          </div>
        </AnimatedSection>
      </section>

      {/* Timeline */}
      <section className="bg-card py-24">
        <div className="container-luxury">
          <AnimatedSection>
            <h2 className="font-display text-3xl md:text-4xl tracking-wider uppercase text-center mb-16">Our Journey</h2>
          </AnimatedSection>
          <div className="max-w-2xl mx-auto">
            {timeline.map((item, i) => (
              <AnimatedSection key={item.year} delay={i * 0.1}>
                <div className="flex gap-8 mb-10">
                  <div className="font-display text-2xl text-accent min-w-[80px]">{item.year}</div>
                  <div className="border-l border-border pl-8">
                    <p className="text-muted-foreground leading-relaxed">{item.text}</p>
                  </div>
                </div>
              </AnimatedSection>
            ))}
          </div>
        </div>
      </section>

      {/* Founder */}
      <section className="container-luxury py-24 grid grid-cols-1 lg:grid-cols-2 gap-12">
        <AnimatedSection>
          <div className="aspect-[4/5] image-zoom">
            <img src={founderImg} alt="Founder" className="w-full h-full object-cover" loading="lazy" width={800} height={1000} />
          </div>
        </AnimatedSection>
        <AnimatedSection delay={0.2}>
          <div className="flex flex-col justify-center">
            <p className="text-xs tracking-[0.3em] uppercase text-muted-foreground mb-3">Founder & Creative Director</p>
            <h2 className="font-display text-3xl md:text-4xl tracking-wider mb-6">Aria Laurent</h2>
            <p className="text-muted-foreground leading-relaxed">
              With over a decade in luxury fashion, Aria founded Aeterna to bridge the gap between timeless craftsmanship
              and contemporary design. Her philosophy centers on intentionality — every seam, every silhouette is a deliberate choice.
            </p>
          </div>
        </AnimatedSection>
      </section>
    </div>
  </PageTransition>
);

export default AboutPage;

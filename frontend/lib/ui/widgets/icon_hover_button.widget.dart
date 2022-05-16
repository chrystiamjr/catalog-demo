import 'package:flutter/material.dart';
import 'package:frontend/util/screen_util.dart';

class IconHoverButton extends StatefulWidget {
  final IconData icon;
  final Function() onTap;

  const IconHoverButton({Key? key, required this.icon, required this.onTap}) : super(key: key);

  @override
  State<IconHoverButton> createState() => _IconHoverButtonState();
}

class _IconHoverButtonState extends State<IconHoverButton> {
  late bool isHovering;
  var defaultPadding = EdgeInsets.all(w(4));
  var duration = const Duration(milliseconds: 150);

  @override
  void initState() {
    setState(() => isHovering = false);
  }

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: widget.onTap,
      child: AnimatedContainer(
        duration: duration,
        decoration: BoxDecoration(
          color: Colors.transparent,
          borderRadius: BorderRadius.circular(100),
        ),
        child: AnimatedContainer(
          duration: duration,
          padding: defaultPadding,
          decoration: BoxDecoration(
            color: isHovering ? Colors.black12 : Colors.transparent,
            borderRadius: BorderRadius.circular(100),
          ),
          child: MouseRegion(
            onEnter: (evt) => setState(() => isHovering = true),
            onExit: (evt) => setState(() => isHovering = false),
            child: Padding(
              padding: defaultPadding,
              child: Icon(
                widget.icon,
                size: h(22),
                color: Colors.white,
              ),
            ),
          ),
        ),
      ),
    );
  }
}

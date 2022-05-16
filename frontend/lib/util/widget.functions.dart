import 'package:flutter/material.dart';
import 'package:frontend/enum/snackbar_type.enum.dart';
import 'package:frontend/ui/widgets/label.widget.dart';
import 'package:get/get.dart';

import 'screen_util.dart';

Widget widgetValidator({
  required Widget child,
  bool validation = false,
}) =>
    validation ? child : Container(width: 0.1);

SnackbarController displaySnackbar({
  String title = 'Alert',
  String code = '',
  String message = 'Simple message',
  SnackbarType type = SnackbarType.info,
}) {
  code = code.isEmpty ? '' : "$code -";

  IconData icon = Icons.info;
  Color color = Colors.blueAccent;

  switch (type) {
    case SnackbarType.error:
      icon = Icons.warning;
      color = Colors.red;
      break;
    case SnackbarType.success:
      icon = Icons.check_circle;
      color = Colors.green;
      break;
    default:
      break;
  }

  return Get.snackbar(
    '',
    '',
    overlayBlur: 10,
    maxWidth: w(450),
    margin: const EdgeInsets.only(top: 25),
    backgroundColor: color,
    icon: Center(
      child: Padding(
        padding: const EdgeInsets.only(left: 20),
        child: Icon(icon, size: w(20), color: Colors.white),
      ),
    ),
    titleText: Center(
      child: Label(
        title,
        size: 16,
        color: Colors.white,
        weight: FontWeight.bold,
      ),
    ),
    messageText: Center(
      child: Padding(
        padding: const EdgeInsets.symmetric(horizontal: 20),
        child: Label(
          '$code $message'.trim(),
          size: 12,
          color: Colors.white,
        ),
      ),
    ),
  );
}

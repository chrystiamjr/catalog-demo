import 'package:flutter/material.dart';
import 'package:frontend/ui/theme.dart';
import 'package:frontend/ui/widgets/input.widget.dart';
import 'package:frontend/ui/widgets/label.widget.dart';
import 'package:frontend/util/screen_util.dart';
import 'package:frontend/util/singleton_memory.dart';
import 'package:get/get.dart';
import 'package:sliding_up_panel/sliding_up_panel.dart';

import '../controller.dart';

class FileSup extends StatelessWidget {
  final Widget child;
  const FileSup({Key? key, required this.child}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return SlidingUpPanel(
      minHeight: 0,
      maxHeight: h(350),
      backdropColor: Colors.black26,
      color: BaseColors.primary,
      controller: SingletonMemory.getInstance().slider,
      isDraggable: false,
      backdropEnabled: true,
      defaultPanelState: PanelState.CLOSED,
      panel: _panel(),
      body: child,
    );
  }
}

Widget _panel() {
  ProductListController controller = Get.find<ProductListController>();

  return Column(
    mainAxisAlignment: MainAxisAlignment.center,
    crossAxisAlignment: CrossAxisAlignment.center,
    children: [
      SizedBox(
        child: Label(
          'Provide your administrator credentials to be able to upload a new spreadsheet',
          size: 18,
          color: Colors.white,
          weight: FontWeight.bold,
        ),
      ),
      Card(
        elevation: 4,
        color: Colors.grey.shade300,
        margin: EdgeInsets.symmetric(vertical: h(30), horizontal: w(450)),
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
        child: Container(
          padding: const EdgeInsets.all(35),
          child: Obx(
            () => Column(
              mainAxisAlignment: MainAxisAlignment.center,
              crossAxisAlignment: CrossAxisAlignment.center,
              children: [
                Padding(
                  padding: const EdgeInsets.all(8.0),
                  child: Input(
                    width: w(300),
                    icon: Icons.person,
                    label: 'Username:',
                    hint: 'admin',
                    controller: controller.usernameCtrl.value,
                    // onChanged: (val) => controller.usernameCtrl.value.text = val,
                  ),
                ),
                Padding(
                  padding: const EdgeInsets.all(8.0),
                  child: Input(
                    width: w(300),
                    isPwd: true,
                    icon: Icons.lock,
                    label: 'Password:',
                    hint: 'admin',
                    controller: controller.passwordCtrl.value,
                  ),
                ),
                Padding(
                  padding: const EdgeInsets.all(8.0),
                  child: MaterialButton(
                    color: BaseColors.secondary,
                    onPressed: () => controller.validateCredentials(),
                    child: Padding(
                      padding: const EdgeInsets.all(15.0),
                      child: Label(
                        'Access and select file',
                        color: Colors.white,
                      ),
                    ),
                  ),
                )
              ],
            ),
          ),
        ),
      ),
    ],
  );
}
